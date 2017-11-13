<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/11/1
 * Time: 16:07
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\ContestModel;
use App\Model\ContestProblemModel;
use App\Model\ContestTeamModel;
use App\Model\ProblemModel;
use App\Model\QuestionModel;
use App\Model\StatusModel;
use App\Model\TeamModel;
use App\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminContestController extends Controller
{
    public function lists(Request $request)
    {
        $lists = ContestModel::get();
        return view('admin.contest_list', [
            'lists' => $lists,
            'menu' => 'contest',
        ]);
    }
    
    public function del(Request $request)
    {
        $cid = $request->get('cid');
        $options = $request->get('options');
        if(count($options) == 0) {
            return response()->json(['status' => false]);
        } else {
            if(in_array('0', $options)) {
                ContestModel::destroy($cid);
            }
            if(in_array('1', $options)) {
                StatusModel::where('contest_id', $cid)->delete();
            }
            if(in_array('2', $options)) {
                ContestTeamModel::where('contest_id', $cid)->delete();
            }
            if(in_array('3', $options)) {
                ContestProblemModel::where('contest_id', $cid)->delete();
            }
            if(in_array('4', $options)) {
                QuestionModel::where('contest_id', $cid)->delete();
            }
            
            return response()->json([
                'status' => true
            ]);
        }
    }

    public function edit(Request $request, $cid = null)
    {
        $teamLists = TeamModel::get();

        if(is_null($cid))
            return view('admin.contest' , [
                'teamLists' => $teamLists,
            ]);
        else {
            $contest = ContestModel::where('contest_id', $cid)->first();
            $proLists = ContestProblemModel::select('contest_pro.pro_id', 'inner_id', 'pro_title', 'max_score')
                ->leftJoin('problem', 'problem.pro_id', '=', 'contest_pro.pro_id')
                ->where('contest_id', $cid)
                ->orderBy('inner_id')
                ->get();
            $cteams = ContestTeamModel::select('team_name', 'team.team_id')
                ->leftJoin('team', 'contest_team.team_id', '=', 'team.team_id')->where('contest_id', $cid)->get();
            return view('admin.contest', [
                'contest' => $contest,
                'cid' => $cid,
                'proLists' => $proLists,
                'teamLists' => $teamLists,
                'cteams' => $cteams,
                'menu' => 'contest',
            ]);
        }
    }

    public function pro_check(Request $request)
    {
        $pid = $request->get('pro_id');
        $pro = ProblemModel::select('pro_title')->where('pro_id', $pid)->first();
        if(!is_null($pro)) {
            return response()->json([
                'status' => true,
                'pro_title' => $pro->pro_title,
            ]);
        } else {
            return response()->json([
                'status' => false,
            ]);
        }
        
    }

    public function save(Request $request)
    {
        $cid = $request->get('contest_id', null);
        $cname = $request->get('contest_name');
        $cpass = $request->get('contest_pass');
        $cstime = $request->get('c_stime');
        $cetime = $request->get('c_etime');
        $prolist = $request->get('prolist');
        $au = $request->get('auNum');
        $ag = $request->get('agNum');
        $cu = $request->get('cuNum');
        $fe = $request->get('feNum');
        $oi = $request->get('oi');
        $options = $request->get('options');
        $teams = $request->get('teams');
        
        if(is_null($cname) || strlen($cname) == 0 || $cetime <= $cstime)
            return response()->json([
                'status' => false,
            ]);
        
        if(is_null($cid)) { //插入比赛数据
            $contest = new ContestModel();
            $contest->contest_name = $cname;
            $contest->contest_pass = $cpass;
            $contest->c_stime = $cstime;
            $contest->c_etime = $cetime;
            $contest->au = $au;
            $contest->ag = $ag;
            $contest->cu = $cu;
            $contest->fe = $fe;
            $contest->oi = $oi;
            $contest->options = $options;
            $contest->user_id = Session::get('user_id');
            $contest->save();

            $cid = $contest->contest_id;

            $arr = [];
            if($cpass == 2 && count($teams) > 0) {
                foreach ($teams as $t) {
                    $arr[] = ['team_id' => $t, 'contest_id' => $cid];
                }
                DB::table('contest_team')->insert($arr);
            }

            $arr = [];
            if(count($prolist) > 0) {
                foreach ($prolist as $pro) {
                    if($oi == 0)
                        $pro[2] = -1;
                    $arr[] = ['inner_id' => $pro[0], 'pro_id' => $pro[1], 'max_score' => $pro[2], 'contest_id' => $cid];
                }
                DB::table('contest_pro')->insert($arr);
            }
            return response()->json([
                'status' => true,
                'contest_id' => $cid,
            ]);
        } else {
            ContestModel::where('contest_id', $cid)->update([
                'contest_name' => $cname,
                'c_stime' => $cstime,
                'c_etime' => $cetime,
                'au' => $au,
                'ag' => $ag,
                'cu' => $cu,
                'fe' => $fe,
                'oi' => $oi,
                'options' => $options,
                'contest_pass' => $cpass,
            ]);

            /**
             * 先删除，再重建
             */
            ContestTeamModel::where('contest_id', $cid)->delete();

            if($cpass == 2 && count($teams) > 0) {
                $arr = [];
                foreach ($teams as $t) {
                    $arr[] = ['team_id' => $t, 'contest_id' => $cid];
                }
                DB::table('contest_team')->insert($arr);
            }
            $cpros = ContestProblemModel::select('pro_id')->where('contest_id', $cid)->get();

            /**
             * 如果目前没有题目，且提交数据有题目，可以直接进行插入
             * 如果当前有题目，需要进行判断，首先统计新的题目，并插入，然后更新已有题目，最后删除失效题目及其提交记录！
             */
            if(is_null($cpros) && count($prolist) > 0) {
                foreach ($prolist as $pro) {
                    if ($oi == 0)
                        $pro[2] = -1;
                    $insertArr[] = ['inner_id' => $pro[0], 'pro_id' => $pro[1], 'max_score' => $pro[2], 'contest_id' => $cid];
                }
                if(isset($insertArr) && count($insertArr) > 0)
                    DB::table('contest_pro')->insert($insertArr);

            } else {
                $cpros = $cpros->toArray();  //contest problem lists
                if (count($prolist) > 0) {

                    $spros = array_column($prolist, 1);  //submit problem lists
                    foreach ($prolist as $key => $p) {
                        if (!in_array(['pro_id' => $p[1]], $cpros)) {
                            if ($oi == 0)
                                $p[2] = -1;
                            $insertArr[] = ['inner_id' => $p[0], 'pro_id' => $p[1], 'max_score' => $p[2], 'contest_id' => $cid];
                            unset($prolist[$key]);  //删除 不给之后的更新带来问题
                        }
                    } //在新列表中，不在旧列表的要插入

                    if (isset($insertArr) && count($insertArr) > 0)
                        DB::table('contest_pro')->insert($insertArr);


                    foreach ($prolist as $p) {
                        if ($oi == 0)
                            $p[2] = -1;
                        ContestProblemModel::where([['contest_id', $cid], ['pro_id', $p[1]]])->update([
                            'inner_id' => $p[0], 'max_score' => $p[2],
                        ]);
                    }  //更新数据部分
                } else {
                    $spros = [];
                }

                foreach ($cpros as $p) {
                    if (!in_array($p['pro_id'], $spros)) {
                        ContestProblemModel::where([['contest_id', $cid], ['pro_id', $p['pro_id']]])->delete();
                        StatusModel::where([['contest_id', $cid], ['pro_id', $p['pro_id']]])->delete();
                    }
                }  //在旧列表中，不在新列表的要删除
            }
            return response()->json([
                'status' => true,
                'contest_id' => $cid,
            ]);
        }
    }

    public function balloon(Request $request)
    {
        $lists = ContestModel::select('contest_id', 'contest_name')->get();//->where('c_etime', '>', time())->get();
        return view('admin.contest_balloon', [
            'menu' => 'contest',
            'lists' => $lists,
        ]);

    }

    public function get_balloon(Request $request)
    {
        $cid = $request->get('cid');
        $hidden = $request->get('hidden');

        $submits = StatusModel::select('submit_id', 'inner_id', 'submit_time', 'username', 'nickname', 'seat')
            ->leftJoin('users', 'users.user_id', '=', 'status.user_id')
            ->leftJoin('contest_pro', 'status.pro_id', '=', 'contest_pro.pro_id')
            ->orderBy('submit_id', 'DESC');
        if($hidden == 1) {
            $submits = $submits->where([['status.contest_id', $cid], ['balloon', 0], ['status', 4], ['seat', '!=', '']])->take(20)->get();
        } else {
            $submits = $submits->where([['status.contest_id', $cid], ['balloon', 0], ['status', 4]])->take(20)->get();
        }
        return response()->json([
            'status' => true,
            'list' => $submits,
        ]);
    }

    public function send_balloon(Request $request) {
        $sid = $request->get('sid');
        StatusModel::where('submit_id', $sid)->update(['balloon' => 1]);
        return response()->json([
            'status' => true,
        ]);
    }
}