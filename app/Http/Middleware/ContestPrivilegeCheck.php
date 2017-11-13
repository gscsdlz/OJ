<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/10/26
 * Time: 10:25
 */

namespace App\Http\Middleware;


use App\Model\ContestModel;
use App\Model\ContestTeamModel;
use App\Model\UserModel;
use Illuminate\Http\Request;
use Closure;
use Illuminate\Support\Facades\Session;

class ContestPrivilegeCheck
{
    public function handle(Request $request, Closure $next)
    {
        $pri = Session::get('privilege');
        $uid = Session::get('user_id');
        $cid = $request->route()->cid;
        if(is_null($uid)) {
            return response(view('404',[
                'cid' => $cid, 'menu' => 'contest@show','info' => '请先登录！'
            ]), 404);
        }
        if($pri == 1)
            return $next($request);

        $contest = ContestModel::select('c_stime', 'c_etime', 'contest_pass')->where('contest_id', $cid)->first();

        if (time() < $contest->c_stime)
            return response(view('404',[
                'cid' => $cid, 'menu' => 'contest@show','info' => '比赛还未开始，请耐心等待！'
            ]), 404);


        if ($contest->contest_pass != 1) {
            $teams = ContestTeamModel::select('team_id')->where('contest_id', $cid)->get()->toArray();

            $uteam = UserModel::select('team_id')->where('user_id', $uid)->first()->team_id;
            if(!in_array(['team_id' => $uteam], $teams))
                return response(view('404',['info' => '私有比赛，仅允许指定小组进入']), 404);
        }
        return $next($request);
    }
}