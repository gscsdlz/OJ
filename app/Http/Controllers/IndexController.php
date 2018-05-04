<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/26
 * Time: 11:14
 */

namespace App\Http\Controllers;

use App\Model\RandModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    public function help()
    {
        return view('help', ['menu' => 'index@help']);
    }

    public function index()
    {
        return view('index');
    }

    public function about()
    {
        return view('about', ['menu' => 'index@about']);
    }

    public function login()
    {
        if(Session::has('user_id'))
            return response()->redirectTo('index');
        else
            return view('login');
    }

    public function test()
    {
        /*$l = DB::table('sim')->select('s_id', 'sim_s_id', 'sim')->where('contest_id', 31)->orWhere('contest_id', 32)->get();
        $res = [];
        foreach ($l as $r) {

            $u1 = DB::table('status')->select('status.submit_id','username', 'nickname', 'code_length', 'submit_time', 'inner_id')
            ->where('status.submit_id', $r->s_id)
                ->leftJoin('codes', 'codes.submit_id', '=', 'status.submit_id')
                ->leftJoin('contest_pro', function($join){
                    $join->on('contest_pro.pro_id', '=', 'status.pro_id')->on('contest_pro.contest_id', '=', 'status.contest_id');
                })
                ->leftJoin('users', 'users.user_id', '=', 'status.user_id')->first();

            $u2 = DB::table('status')->select('status.submit_id','username', 'nickname', 'code_length', 'submit_time', 'inner_id')
                ->where('status.submit_id', $r->sim_s_id)
                ->leftJoin('codes', 'codes.submit_id', '=', 'status.submit_id')
                ->leftJoin('contest_pro', function($join){
                    $join->on('contest_pro.pro_id', '=', 'status.pro_id')->on('contest_pro.contest_id', '=', 'status.contest_id');
                })
                ->leftJoin('users', 'users.user_id', '=', 'status.user_id')->first();

            $res[] = [$r->sim, $u1, $u2];
        }
        return view('admin.sim_code',[
            'res' => $res,
        ]);
       $res = DB::table('users')->select('nickname','username', 'user_id')->where('team_id', 44)->get();
        foreach ($res as $row) {
            $path = substr($row->username, 1).'('.$row->nickname.')';
            $c = DB::table('status')->select('status.submit_id', 'status','code')->where('user_id', $row->user_id)->where('contest_id', 32)
                ->leftJoin('codes', 'codes.submit_id', '=', 'status.submit_id')
                ->get();
            foreach($c as $r)
            {
                if($r->status == 4) {
                    file_put_contents('/var/www/html/NOJ/public/codes/'.$path.'/ac/'.$r->submit_id.'.c', $r->code);
                } else {
                    file_put_contents('/var/www/html/NOJ/public/codes/'.$path.'/wa/'.$r->submit_id.'.c', $r->code);
                }
            }
           //dd("stop");
        }
        echo 'ok';*/
    }

    public function rand(Request $request)
    {
      /*  for($i = 0; $i < 2000; $i++) {
            RandModel::insert([
                'rand' => rand(1000, 9999),
                'used' => 0,
            ]);
        }*/

        if(strpos($request->server('HTTP_USER_AGENT'), 'MicroMessenger') === false ) {
            return view('useWechatOnly');
        }


        $info = $request->cookie('info', null);
        if(is_null($info)) {

            if(!is_null($request->get('token', null))) {

                DB::transaction(function () use (&$info) {
                    $info = DB::table('rand')->select('rand')->where('used', '0')->first();
                    $info = $info->rand;
                    DB::table('rand')->where('rand', $info)
                        ->update(['used' => '1']);
                });

                return response()->redirectTo('/check')
                    ->cookie('info', $info, 48 * 60);

            } else {
                return view('useWechatOnly');
            }
        }
        return response()->view('rand', ['info' => $info])
            ->cookie('info', $info, 48 * 60);
    }


    /**
     * 抽奖程序
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function award(Request $request)
    {
        return view('award');
    }

    public function awardAdd(Request $request)
    {
        $rand = $request->get('result');
        $type = $request->get('type_id');

        RandModel::where('rand', $rand)
            ->update(['type'  => $type]);

        return response()->json([
            'status' => true
        ]);
    }

    public function randGet(Request $request)
    {

        $count = RandModel::where('type', '1')->count();
        if($count == 0) {
            $res = RandModel::select('rand')->where([
               ['used', '1'],
               ['type', '0'],
               ['attr', '0'],
            ])->get();
        } else if($count == 1 || $count == 2) {
            $res = RandModel::select('rand')->where([
                ['used', '1'],
                ['type', '0'],
                ['attr', '1'],
            ])->get();
        } else {
            $res = RandModel::select('rand')->where([
                ['used', '1'],
                ['type', '0']
            ])->get();
        }

        return response()->json([
            'status' => true,
            'result' => $res[rand(0, count($res) - 1)]->rand,
        ]);
    }

    public function getAll(Request $request)
    {
        $res = RandModel::select('rand', 'type')->where('type', '!=', '0')
            ->orderBy('type', 'ASC')->get();

        foreach($res as &$row) {
            switch ($row->type) {
                case 1 : $row->type = '一等奖';break;
                case 2 : $row->type = '二等奖';break;
                case 3 : $row->type = '三等奖';break;
            }
        }
        unset($row);
        return response()->json([
            'status' => true,
            'data' => $res,
        ]);
    }
}