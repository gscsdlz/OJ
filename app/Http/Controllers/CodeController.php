<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/8/1
 * Time: 8:47
 */

namespace App\Http\Controllers;


use App\Model\CEModel;
use App\Model\CodeModel;
use App\Model\ContestModel;
use App\Model\ContestProblemModel;
use App\Model\ProblemModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class CodeController extends Controller
{
    public function show(Request $request, $sid, $cid = 0)
    {
        $codes = CodeModel::select('submit_id','code')->where('submit_id', $sid)->first();
        $submitInfo = $codes->submit()->select('*')->first();
        $userInfo = $submitInfo->user()->select('username', 'nickname')->first();
        if($cid != 0) {
            $submitInfo->pro_id = ContestProblemModel::select('inner_id')->where([
                ['pro_id', $submitInfo->pro_id],
                ['contest_id', $cid]])->first()->inner_id;
        }
        return view('code',[
            'cid' => $cid,
            'code' => $codes,
            'submitInfo' => $submitInfo,
            'userInfo' => $userInfo,
        ]);
    }

    public function compiler_show(Request $request, $sid, $cid = 0)
    {
        $ce = CEModel::select("submit_id", 'info')->where('submit_id', $sid)->first();
        $submitInfo = $ce->submit()->select('*')->first();
        $userInfo = $submitInfo->user()->select('username', 'nickname')->first();
        if($cid != 0) {
            $submitInfo->pro_id = ContestProblemModel::select('inner_id')->where([
                ['pro_id', $submitInfo->pro_id],
                ['contest_id', $cid]])->first()->inner_id;
        }
        return view('compiler',[
            'cid' => $cid,
            'ce' => $ce,
            'submitInfo' => $submitInfo,
            'userInfo' => $userInfo,
        ]);
    }

    public function submit(Request $request)
    {
        $pid = $request->get('pro_id', null);
        $code = $request->get('codes', null);
        $lang = $request->get('lang', null);
        $cid = $request->get('cid', 0);
        $uid = Session::get('user_id', null);

        if(is_null($uid) || is_null($pid) || is_null($code) || is_null($lang)) {
            return response()->json(
                [
                    'status' => false,
                    'info' => 'error',
                ]
            );
        }
        $score = -1;   //判题机 通过识别score的值来判断是否启用OI模式
        if(!is_null($cid) && $cid > 0) {
            $pro = ContestProblemModel::select('pro_id', 'max_score')->where([
                ['inner_id', $pid],
                ['contest_id', $cid]
            ])->first();
            $contest = ContestModel::select('c_etime', 'c_stime')->where('contest_id', $cid)->first();

            if(is_null($pro) || is_null($contest)) {
                return response()->json(
                    [
                        'status' => false,
                        'info' => 'Wrong PID OR CID',
                    ]
                );
            } else if($contest->c_etime < time() || $contest->c_stime > time()) {
                return response()->json(
                    [
                        'status' => false,
                        'info' => 'Time Error',
                    ]
                );
            } else {
                $pid = $pro->pro_id;
                $score = $pro->max_score;
            }

        } else {
            $pro = ProblemModel::select('visible')->where('pro_id', $pid)->first();
            if(!is_null($pro) && $pro->visible == 0 && Session::get('privilege') != 1) {
                return response()->json([
                    [
                        'status' => false,
                        'info' => 'error',
                    ]
                ]);
            }  //对隐藏题目且不在比赛中的，进行检查
        }

        /*DB::transaction(function() use($code){
           DB::table('codes')->insert(['code' => $code]);
           DB::table('status')->insert([]);
        });*/
        Session::set('lang', $lang);
        DB::beginTransaction();
        $sid1 = DB::table('codes')->insertGetId(['code' => $code]);
        $sid2 = DB::table('status')->insertGetId([
            'pro_id' => $pid,'user_id'=>$uid,'lang'=>$lang,
            'submit_time' => time(),
            'contest_id' => $cid,
            'score' => $score,
            'code_length' => strlen($code)
        ]);
        if($sid1 == $sid2) {
            DB::commit();
            Redis::rpush('submit_id', $sid1);
            return response()->json(['status' => true]);
        } else {
            DB::rollback();
            return response()->json(
                [
                    'status' => false,
                    'info' => 'SystemError',
                ]
            );
        }
    }
}