<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/5/25
 * Time: 15:08
 */

namespace App\Http\Controllers;

use App\Model\PointModel;
use App\Model\ProblemModel;
use App\Model\StatusModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProblemController extends Controller
{

    public function page(Request $request, $page = 1)
    {
        if($page < 1)
            $page = 1;
        $pms = config('web.ProblemPageMax');
        $lists = ProblemModel::select('pro_id', 'pro_title')->where([
            ['visible', 1],
            ['pro_id', '>=', 1000 + ($page - 1) * $pms],
            ['pro_id', '<=', 1000 + ($page) * $pms],
        ])->get();
        $mount = ProblemModel::where('visible', 1)->count();
        $mount = (int)(($mount - 1) / $pms + 1);
        $userID = Session::get('user_id', null);
        /**
         * 这里查询速度很慢 暂无解决办法
         */
        /*foreach ($lists as $pro) {
                $pro->set_ac_num();
                $pro->set_ac_status($userID);
                $pro->set_points();
        }*/
         /**变更为SELECT XXX FROM XXX WHERE pro_id IN (1,2,3,4,5) GROUP BY;
         */

            $pidArr = [];

            foreach ($lists as $pro) {
                $pidArr[] = $pro->pro_id;
            }

            $acs = StatusModel::selectRaw('pro_id, COUNT(*) AS mount')
                ->where([['status', '4'],['contest_id', '0']])
                ->whereIn('pro_id', $pidArr)
                ->groupBy('pro_id')->get()->toArray();
            $acs = array_combine(array_column($acs, 'pro_id'), array_column($acs, 'mount'));

            $was = StatusModel::selectRaw('pro_id, COUNT(*) AS mount')
                ->where('contest_id', '0')
                ->whereIn('pro_id', $pidArr)
                ->groupBy('pro_id')->get()->toArray();;
            $was = array_combine(array_column($was, 'pro_id'), array_column($was, 'mount'));

            if (!is_null($userID)) {
                $userAc = StatusModel::selectRaw('pro_id, COUNT(*) AS mount')
                    ->where([['status', 4], ['contest_id', 0], ['user_id', $userID]])
                    ->whereIn('pro_id', $pidArr)
                    ->groupBy('pro_id')->get()->toArray();;
                $userAc = array_combine(array_column($userAc, 'pro_id'), array_column($userAc, 'mount'));

                $userWa = StatusModel::selectRaw('pro_id, COUNT(*) AS mount')
                    ->where([['user_id', $userID], ['contest_id', 0]])
                    ->whereIn('pro_id', $pidArr)
                    ->groupBy('pro_id')->get()->toArray();;
                $userWa = array_combine(array_column($userWa, 'pro_id'), array_column($userWa, 'mount'));
            }
            $points = PointModel::select('point_name', 'pro_id')
                ->leftJoin('point_pro', 'point.point_id', '=', 'point_pro.point_id')
                ->whereIn('pro_id', $pidArr)
                ->get()->toArray();

            $pArr = [];
            foreach ($points as $row) {
                $pArr[$row['pro_id']][] = $row['point_name'];
            }

            foreach ($lists as $pro) {
                if (isset($acs[$pro->pro_id]))
                    $pro->setACNum($acs[$pro->pro_id]);
                if (isset($was[$pro->pro_id]))
                    $pro->setAllNum($was[$pro->pro_id]);

                if (!is_null($userID)) {
                    if (!isset($userAc[$pro->pro_id]))
                        $fa = 0;
                    else
                        $fa = $userAc[$pro->pro_id];
                    if (!isset($userWa[$pro->pro_id]))
                        $fb = 0;
                    else
                        $fb = $userWa[$pro->pro_id];

                    $pro->setACFlag($fa, $fb);
                }
                if (isset($pArr[$pro->pro_id]))
                    $pro->setPoint($pArr[$pro->pro_id]);
            }

        return view('problem_list', [
            'page' => $page,
            'menu' => 'problem',
            'lists' => $lists, 'mount' =>$mount
        ]);
    }

    public function show(Request $request, $pid)
    {
        $pro = ProblemModel::where('pro_id', $pid)->first();
        $userID = Session::get('user_id', null);
        if (!is_null($userID))
            $pro->set_ac_status($userID);
        $pro->set_ac_num();

        $pri = Session::get('privilege', null);

        if($pro->visible == 0) {
            if (!is_null($pri) && $pri == 1)
                return view('problem', [
                    'menu' => 'problem',
                    'pro' => $pro, 'contest' => 0
                ]);
            else
                return response(view('404'), 404);
        } else {
            return view('problem', [
                'menu' => 'problem',
                'pro' => $pro, 'contest' => 0
            ]);
        }
    }

    public function get_statistics(Request $request)
    {
        $pro_id = $request->get('pro_id', null);
        $cid = $request->get('cid', null);
        if(is_null($cid)) {
            $mounts = StatusModel::selectRaw('status, count(*)')->where([
                ['pro_id', $pro_id],['contest_id', 0]
            ])->groupBy('status')->get();
        } else {
            $mounts = StatusModel::selectRaw('status, count(*)')->where([
                ['pro_id', $pro_id],
                ['contest_id', $cid],
                ])->groupBy('status')->get();
        }

        return $mounts->toJson();
    }

    public function search(Request $request)
    {
        $key = $request->get('key');
        $pros = ProblemModel::select('pro_id', 'pro_title', 'author', 'visible')->where('pro_id', $key)
            ->orWhere('pro_title', 'like', '%'.$key.'%')
            ->orWhere('author', 'like', '%'.$key.'%')->limit(100)->get();
        $request->flashOnly(['key', $key]);

        return view('pro_search',[
            'menu' => 'problem',
            'pros' => $pros
        ]);
    }
}