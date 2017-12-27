<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/8/2
 * Time: 10:29
 */

namespace App\Http\Controllers;


use App\Model\ContestModel;
use App\Model\QQOAuthModel;
use App\Model\StatusModel;
use App\Model\TeamModel;
use App\Model\UserModel;
use App\Model\VCodeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $vcode = $request->get('vcode');


        if(strlen($username) == 0 || strlen($password) == 0)
            return response()->json([
                'status' => false
            ]);
        if($vcode == Session::get('vcode') || config('web.vcode') == false) {
            $res = UserModel::select('password', 'username', 'privilege', 'user_id')->where('username', $username)->first();

            if (!is_null($res) && sha1($password) == $res->password && $res->username == $username) {
                Session::put('username', $res->username);
                Session::put('privilege', $res->privilege);
                Session::put('user_id', $res->user_id);

                UserModel::where('user_id', $res->user_id)->update(['lasttime' => time(), 'lastip' => $request->server('HTTP_X_REAL_IP')]);
                return response()->json([
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'status' => false
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'info' => 'error vcode',
            ]);
        }
    }

    public function findPass(Request $request)
    {
        $username = $request->get('username');
        $user = UserModel::select('email')->where('username', $username)->first();
        if(is_null($user) || is_null($user->email) || strlen($user->email) < 5 ) {
            return response()->json(['status' => false]);
        } else {
            $email = $user->email;
            $token = md5($username.rand(0, 1000));
            $url = URL('user/resetPass?token=').$token;
            Redis::set('findPassToken:'.$token, $username);
            Redis::expire('findPassToken:'.$token, 5 * 60);

            Mail::send('mail.findPass' ,['url' => $url, 'username' => $username, 'ua' => $request->server('HTTP_USER_AGENT'), 'ip' => $request->server('HTTP_X_REAL_IP')] , function ($message) use ($email) {
                $to = $email;
                $message->to($to)->subject("中北大学Online Judge免密登录邮件");
            });
            if(empty(Mail::failures()))
                return response()->json(['status' => true, 'info' => $email[0].'******'.substr($email, strpos($email, '@'))]);
            else
                return response()->json(['status' => false]);
        }
    }

    public function resetPass(Request $request)
    {
        $token = $request->get('token');
        $username = Redis::get('findPassToken:'.$token);

        if(!is_null($username)) {
            $res = UserModel::select('username', 'privilege', 'user_id')->where('username', $username)->first();
            Session::put('username', $res->username);
            Session::put('privilege', $res->privilege);
            Session::put('user_id', $res->user_id);
            Redis::del('findPassToken:'.$token);
        }
        return view('resetPass');
    }


    public function show(Request $request, $username)
    {
        $userInfo = UserModel::where('username', $username)->first();
        $teamInfo = $userInfo->team()->first();
        $teams = TeamModel::get()->toArray();
        $acList = StatusModel::select('pro_id')->distinct()->where([
            ['contest_id', 0],
            ['user_id', $userInfo->user_id],
            ['status', 4],
        ])->get()->toArray();

        $waList = StatusModel::select('pro_id')->distinct()->where([
            ['contest_id', 0],
            ['user_id', $userInfo->user_id],
            ['status', '!=', 4],
        ])->whereNotIn('pro_id', $acList)->get()->toArray();

        $submitCount = StatusModel::selectRaw('status, COUNT(status)')->where([
            ['contest_id', 0],
            ['user_id', $userInfo->user_id],
        ])->groupBy('status')->get()->toArray();

        $contestLists = ContestModel::leftJoin('status', 'status.contest_id', '=', 'contest.contest_id')
            ->selectRaw('contest.contest_id, contest_name, COUNT(DISTINCT pro_id) AS sum')
            ->where([
                ['status.user_id', $userInfo->user_id],
                ['status', 4],
            ])
            ->groupBy('contest.contest_id')->get();
        $submits = array(
            0,0,0,0,0,0,0,0
        );
        foreach ($submitCount as $row) {
            $submits[$row['status'] - 4] = $row['COUNT(status)'];
        }

        return view('user', [
            'userInfo' => $userInfo,
            'teamInfo' => $teamInfo,
            'waList' => $waList,
            'acList' => $acList,
            'submits' => $submits,
            'teams' => $teams,
            'contestLists' => $contestLists,
        ]);
    }

    public function vcode()
    {
        $max = VCodeModel::count();
        $lists = VCodeModel::where('vid', rand(1, $max))->first()->toArray();
        $lists = array_values($lists);
        $answer =$lists[$lists[6] + 1];
        
        $options = [$lists[2],$lists[3],$lists[4], $lists[5]];
        shuffle($options);
        
        foreach ($options as $key=>$row) {
            if($answer == $row) {
                $answer = $key;
                break;
            }
        }
        Session::put('vcode', $answer);
        return response()->json([
            'status' => true,
            'problem' => $lists[1],
            'options' => $options,
        ]);
    }

    public function logout(Request $request)
    {
        Session::flush();
        return response()->json(
            ['status' => true]
        )->cookie ( 'PHPSESSID', '', time () - 3600, '/', '', 0, 0 );;
    }

    public function update(Request $request)
    {
        $uid = Session::get('user_id');

        $email = $request->get('email', null); //邮箱可能需要后期配合邮件服务器一起
        $qq = $request->get('qq');
        $motto = $request->get('motto');

        $pass1 = $request->get('password');
        $pass2 = $request->get('password2');

        $user = UserModel::select('activate', 'team_id')->where('user_id', $uid)->first();
        $act = $user->activate;
        $oldID = $user->team_id;


        if($act == 1) {
            $nickname = $request->get('nickname');
            $tid = $request->get('group');
            $seat = $request->get('seat');

            UserModel::where('user_id', $uid)->update(['nickname' => $nickname, 'seat' => $seat,]);

            if (TeamModel::select('private')->where('team_id', $oldID)->first()->private == 0 && TeamModel::select('private')->where('team_id', $tid)->first()->private == 0) {

                UserModel::where('user_id', $uid)->update([
                    'team_id' => $tid,
                ]);
            }  //检查私有小组问题



        }
        if(strlen($email) == 0)
            $email = null;
        $count = UserModel::where([['email', $email], ['user_id', '!=', $uid]])->count();
        if($count == 1)
            return response()->json(['status' => false,'info' => 'emailError']);
        else
            UserModel::where('user_id', $uid)->update(['email' => $email]);


        UserModel::where('user_id', $uid)->update(['qq' => $qq, 'motto' => $motto]);


        if($pass1 != '' && $pass1 == $pass2) {
            UserModel::where('user_id', $uid)->update(['password' => sha1($pass1)]);
        } else if($pass1 != '' && $pass2 != ''){
            return response()->json([
                'status' => false,
                'info' => 'passError'
            ]);
        }

        return response()->json(['status' => true]);

    }

    public function upload(Request $request)
    {
        if($request->file('file')->isValid()) {
            $uid = Session::get('user_id');
            $allows = ['jpeg', 'gif', 'png'];
            $extension = $request->file->extension();
            if (in_array($extension, $allows)) {
                $user = UserModel::select('headerpath')->where('user_id', $uid)->first();
                $path = $request->file->path();
                $nname = $uid.time().'.'.$extension;
                $status = move_uploaded_file($path, './image/header/'.$nname);
                if(strpos($user->headerpath, 'default') === false && strpos($user->headerpath, 'https') === false) {
                    unlink('./image/header/' . $user->headerpath);
                }
                UserModel::where('user_id', $uid)->update(['headerPath' => $nname]);
                if($status == true)
                    return response()->json(['status' => true]);
            }

        }
        return response()->json(['status' => false]);

    }

    public function register(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');
        $nickname = $request->get('nickname');
        $email = $request->get('email');

        if(is_null($username) || is_null($email) || is_null($password)) {
            return response()->json(['status' => false]);
        } else {
            if(preg_match('/^[A-Za-z0-9_]+$/', $username) == 0)
                return response()->json(['status' => false, 'info' => '用户名不符合规则']);

            $c1 = UserModel::where('username', $username)->count();
            if($c1 != 0) {
                return response()->json([
                    'status' => false,
                    'info' => '用户名重复',
                ]);
            }

            $c2 = UserModel::where('email', $email)->count();
            if($c2 != 0)
                return response()->json([
                    'status' => false,
                    'info' => '邮箱重复',
                ]);

            $token = md5(time() . rand(10000, 99999));
            $url = URL('activate?token=').$token;

            Mail::send('mail.register' ,['username' => $username, 'url' => $url] , function ($message) use ($email) {
                $to = $email;
                $message->to($to)->subject("中北大学Online Judge注册激活邮件");
            });

            if(empty(Mail::failures())) {
                Redis::set('regToken:'.$token, json_encode([
                    'username' => $username,
                    'nickname' => $nickname,
                    'password' => $password,
                    'email' => $email,
                ]));
                Redis::expire('regToken:'.$token, 5 * 60);
                return response()->json([
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'info' => '发送失败！'
                ]);
            }
        }
    }

    public function do_register(Request $request)
    {
        $token = $request->get('token');
        $user = Redis::get('regToken:'.$token);
        if(!is_null($user)) {
            $info = json_decode($user, true);
            $id = DB::table('users')->insertgetId([
                'username' => $info['username'],
                'nickname' => $info['nickname'],
                'password' => sha1($info['password']),
                'email' => $info['email'],
            ]);

            if($id > 0) {
                Session::put('username', $info['username']);
                Session::put('privilege', -1);
                Session::put('user_id', $id);
                Redis::del('regToken:'.$token);
            }
        }

        return view('register');

    }

    public function ip_addr_map(Request $request)
    {
        $ip = $request->get('ip_addr');

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, "http://ip.chinaz.com/" . $ip );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $output = curl_exec ( $ch );
        curl_close ( $ch );
        $location = strrpos ( $output, "Whwtdhalf w50-0" );
        $i = 0;
        for($i = $location; $i < $location + 100; $i ++) {
            if ($output [$i] == '<')
                break;
        }
        $addr = substr ( $output, $location + 17, $i - ($location + 17) );

        return response()->json(['addr' => $addr]);

    }

    /**
     * User: Daemon
     * Time: 2017年12月26日
     * QQ OAuth登录
     * @param $request
     * @return class
     */
    public function qqLogin(Request $request)
    {
        $code = $request->get('code', null);
        if(is_null($code)) {
            Session::put('state', md5(uniqid(rand(), TRUE)));
            return response()->redirectTo("https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" . config('web.QQ_APPID') . "&redirect_uri=" . urlencode(config('web.QQ_REDIRECT_URL')) . "&state=".Session::get('state')."&scope=get_user_info");
        } else {

            if($request->get('state') == Session::get('state')) {
                $token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=" . config('web.QQ_APPID') . "&client_secret=" . config('web.QQ_APPKEY') . "&code=" . $code . "&redirect_uri=" . urlencode(config('web.QQ_REDIRECT_URL'));
                $res = file_get_contents($token_url);

                if(strpos($res, 'callback') === false) {
                    $arg = explode("&", $res);
                    $info = [];
                    foreach ($arg as $a) {
                        $tmp = explode("=", $a);
                        $info[$tmp[0]] = $tmp[1];
                    }
                    $openid_url = "https://graph.qq.com/oauth2.0/me?access_token=".$info['access_token'];
                    $res = file_get_contents($openid_url);
                    $openid = substr($res, strpos($res, "openid") + 9, 32);

                    $user = QQOAuthModel::select('user_id')->where('openid', $openid)->first();
                    if(is_null($user)) {//发起注册或者绑定
                        $info_url = "https://graph.qq.com/user/get_user_info?access_token=".$info['access_token']."&oauth_consumer_key=".config('web.QQ_APPID')."&openid=".$openid;
                        $res = file_get_contents($info_url);
                        $res = json_decode($res, true);
                        Session::put('openid', $openid);

                        if(isset($res['figureurl_qq_2']) && strlen($res['figureurl_qq_2']) > 0)
                            Session::put('imgPath', $res['figureurl_qq_2']);
                        else
                            Session::put('imgPath', $res['figureurl_qq_1']);

                        return view('oauth_bind', [
                            'user' => $res,
                        ]);
                    } else {
                        $res = UserModel::select('username', 'privilege')->where('user_id', $user->user_id)->first();
                        Session::put('username', $res->username);
                        Session::put('privilege', $res->privilege);
                        Session::put('user_id', $user->user_id);
                        return response()->redirectTo('/index');
                    }
                }
            }
            return view('oauth_bind', [
                'user' => ['msg' => '会话失败，请重试']
            ]);
        }
    }

    public function qq_bind(Request $request)
    {
        if(Session::has('openid')) {
            $username = $request->get('username');
            $password = $request->get('password');
            $nickname = $request->get('nickname');
            $openid = Session::get('openid');
            $user = UserModel::select('user_id', 'password', 'privilege','username')->where('username', $username)->first();
            if(is_null($user)) {
                if(preg_match('/^[A-Za-z0-9_]+$/', $username) == 0)
                    return response()->json(['status' => false, 'info' => '用户名不符合规则']);

                $tmp = file_get_contents(Session::get('imgPath'));
                $path = time() . rand(10000, 99999).".png";
                Storage::put('image/header/'.$path, $tmp);

                $id = DB::table('users')->insertgetId([
                    'username' => $username,
                    'nickname' => $nickname,
                    'password' => sha1($password),
                    'headerpath' => $path
                ]);
                DB::table('qq_oauth')->insert([
                    'user_id' => $id,
                    'openid' => $openid,
                ]);

                Session::put('username', $username);
                Session::put('privilege', -1);
                Session::put('user_id', $id);

                return response()->json(['status' => true]);
            } else {
                if(sha1($password) != $user->password)
                    return response()->json(['status' => false, 'info' => '密码错误']);
                else {
                    DB::table('qq_oauth')->insert([
                        'user_id' => $user->user_id,
                        'openid' => $openid,
                    ]);

                    Session::put('username', $user->username);
                    Session::put('privilege', $user->privilege);
                    Session::put('user_id', $user->user_id);
                    return response()->json(['status' => true]);
                }
            }
        } else {
            return response()->json(['status' => false, 'info' => '校验失败，请重新登录']);
        }
    }
}
