<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/1
 * Time: 9:53
 */

namespace App\Http\Controllers;


use App\Model\StatusModel;
use App\Model\UserModel;
use App\User;
use Illuminate\Http\Request;

class RankController extends Controller
{
    public function show(Request $request, $page = 1)
    {
        if($page <= 0)
            $page = 1;
        $userArr = array();
        $this->get_info($userArr);

        $newArr = array();
        $k = 1;
        $pms = config('web.RankPageMax');
        $start = ($page - 1) * $pms + 1;
        $maxpage = (int)((count($userArr) - 1) / $pms) + 1;

        foreach ($userArr as $key => $user) {

            if ($k >= $start && $k < $start + $pms) {
                $newArr[] = [$key, $user[0], $user[1], $user[2], $user[3], $user[4], $k];
            }
            if ($k++ >= $start + $pms)
                break;
        }
        return view('rank', [
            'menu' => 'rank',
            'users' => $newArr,
            'page' => $page,
            'maxpage' => $maxpage
        ]);

    }
    /**
     *上线以后加入Redis功能
     */
    private function get_info(&$userArr)
    {
        $acStatus = StatusModel::selectRaw('user_id, COUNT(DISTINCT pro_id)')->where([['status', 4],['contest_id', '0']])->groupBy('user_id')->get()->toArray();
        $waStatus = StatusModel::selectRaw('COUNT(status), user_id')->where('contest_id', '0')->groupBy('user_id')->get()->toArray();
        $users = UserModel::select('user_id', 'username', 'nickname', 'motto')->where('activate', '1')->get();

        $userArr = array();
        foreach ($users as $user) {
            $userArr[$user->user_id] = [$user->username, $user->nickname, $user->motto, 0, 0];
        }
        foreach ($acStatus as $acs) {
            if(isset($userArr[$acs['user_id']]))
                $userArr[$acs['user_id']][3] = $acs['COUNT(DISTINCT pro_id)'];
        }
        foreach ($waStatus as $was) {
            if(isset($userArr[$was['user_id']]))
                $userArr[$was['user_id']][4] = $was['COUNT(status)'];
        }
        uasort($userArr, function($a, $b){
            if ($a[3] == 0 && $b[3] == 0)
                return $a[4] < $b[4];
            if ($a[3] == $b[3] && $a[3])
                return $a[4] > $b[4];
            else
                return $a[3] < $b[3];
        });
    }

    public function nearByUser(Request $request, $username)
    {
        $userArr = array();
        $this->get_info($userArr);
        $uid = UserModel::select('user_id')->where('username', $username)->first()->user_id;
        $res = [];
        $header = head($userArr);
        $pos = 0;
        foreach ($userArr as $key => $row) {
            $pos++; //记录当前用户之前有几个人
            if ($key == $uid) {
                $k = 3;
                if($pos == count($userArr) || $pos == count($userArr) - 1) //由于foreach语句获取数据以后就自动移动指针，所以这里需要做一个特判
                    end($userArr);

                while (current($userArr) != $header && $k > 0) { //指针不能移出数组
                    prev($userArr);
                    $k--;
                }

                $rank = $pos - (3 - $k) + 1;
                $k = 5;
                $p = 0;
                while (current($userArr) != false && $k > 0) { //指针可以移出数组，这样可以将最后一个元素加入数组。
                    $res[$p] = current($userArr);
                    $res[$p][] = $rank++;
                    $p++;
                    next($userArr);
                    $k--;
                }
                break;
            }
        }
        return response()->json(
            ['res' => $res]
        );
    }
}