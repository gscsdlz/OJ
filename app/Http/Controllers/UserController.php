<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/8/2
 * Time: 10:29
 */

namespace App\Http\Controllers;


use App\Model\ContestModel;
use App\Model\StatusModel;
use App\Model\TeamModel;
use App\Model\UserModel;
use App\Model\VCodeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

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
        if($vcode == Session::get('vcode')) {
            $res = UserModel::select('password', 'username', 'privilege', 'user_id')->where('username', $username)->first();

            if (!is_null($res) && sha1($password) == $res->password && $res->username == $username) {
                Session::put('username', $res->username);
                Session::put('privilege', $res->privilege);
                Session::put('user_id', $res->user_id);
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

        $email = $request->get('email'); //邮箱可能需要后期配合邮件服务器一起
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

        UserModel::where('user_id', $uid)->update(['qq' => $qq, 'motto' => $motto, 'email' => $email]);


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
                unlink('./image/header/'.$user->headerpath);
                UserModel::where('user_id', $uid)->update(['headerPath' => $nname]);
                if($status == true)
                    return response()->json(['status' => true]);
            }

        }
        return response()->json(['status' => false]);

    }

    public function register(Request $request)
    {
        $username = $request->get('newUsername');
        $password = $request->get('newPassword');
        $password2 = $request->get('newPassword2');
        $nickname = $request->get('newNickname');
        $email = $request->get('email');

        if(is_null($username) || is_null($email) || is_null($password) || $password != $password2) {
            return response()->json(['status' => false]);
        } else {
            $c1 = UserModel::where('username', $username)->count();
            if($c1 != 0) {
                return response()->json([
                    'status' => 'username error',
                ]);
            }
            $c2 = UserModel::where('email', $email)->count();
            if($c2 != 0)
                return response()->json([
                    'status' => 'email error',
                ]);

            $id = DB::table('users')->insertgetId([
                'username' => $username,
                'nickname' => $nickname,
                'password' => sha1($password),
                'email' => $email,
            ]);
            if($id > 0) {
                Session::put('username', $username);
                Session::put('privilege', -1);
                Session::put('user_id', $id);
                return response()->json([
                    'status' => true
                ]);
            }
        }

    }
}