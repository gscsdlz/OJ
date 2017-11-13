<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/7/29
 * Time: 12:57
 */

namespace App\Http\Controllers;

use App\Model\ContestModel;
use App\Model\ContestProblemModel;
use App\Model\StatusModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class StatusController extends Controller
{
    public function status(Request $request)
    {
        $ridl = $request->get('ridl', null);
        $ridr = $request->get('ridr', null);
        $cid = $request->get('cid', 0);
        $pid = $request->get('pid', null);
        $lang = $request->get('lang', null);
        $status = $request->get('status', null);
        $user = $request->get('user', null);

        $q = StatusModel::selectRaw('status.*, username, nickname')->leftJoin('users', 'users.user_id', '=', 'status.user_id');
        if(!is_null($ridl))
            $q = $q->where('submit_id', '<=', $ridl);
        if(!is_null($ridr))
            $q = $q->where('submit_id', '>=', $ridr)->orderBy('submit_id', 'ASC');
        if(!is_null($cid) && $cid > 0)
            $q = $q->where('contest_id', $cid);
        else
            $q = $q->where('contest_id', '0');

        //////特殊处理
        ///
        if(!is_null($pid) && $pid > 0) {
            $oldPid = $pid;
            if(!is_null($cid) && $cid > 0) {
                $pro = ContestProblemModel::select('pro_id')->where([['inner_id', $pid], ['contest_id', $cid]])->first();
                if (!is_null($pro)) {
                    $pid = $pro->pro_id;
                    $q = $q->where('pro_id', $pid);
                    $pid = $oldPid;
                } else {
                    $pid = $oldPid;
                    $hasError = true;
                }
            } else {
                $q = $q->where('pro_id', $pid);
            }
        }
        if(!is_null($lang) && $lang > 0)
            $q = $q->where('lang', $lang);
        if(!is_null($status) && $status > 0)
            $q = $q->where('status', $status);
        if(!is_null($user) && strlen($user) > 0)
            $q = $q->where('username', $user);

        $lists = [];
        $contestInfo = "";
        if(!isset($hasError)) {
            if (!is_null($ridr))
                $lists = $q->take(20)->get()->toArray();
            else
                $lists = $q->orderBy('submit_id', 'DESC')->take(20)->get()->toArray();

            usort($lists, function ($a, $b) {
                return $a['submit_id'] < $b['submit_id'];
            });

            if (isset($lists[0]) && count($lists) >= 1) {
                $ridl = $lists[0]['submit_id'];
                $ridr = $lists[count($lists) - 1]['submit_id'];
            }

            if ($cid != 0) {
                $ids = ContestProblemModel::select('inner_id', 'pro_id')->where('contest_id', $cid)->get()->toArray();
                $ids = array_combine(array_column($ids, 'pro_id'), array_column($ids, 'inner_id'));
                foreach ($lists as &$row) {
                    $row['pro_id'] = $ids[$row['pro_id']];
                }
                unset($row);

            }
        }
        $contestInfo = ContestModel::select('contest_name', 'oi', 'contest_pass', 'options')->where('contest_id', $cid)->first();
        return view('status',[
            'cid' => $cid,
            'menu' => 'status',
            'lists' => $lists,
            'pid' => $pid,
            'contest' => $contestInfo,
            'lang' => $lang,
            'status' => $status,
            'user' => $user,
            'ridl' => $ridl,
            'ridr' => $ridr,
        ]);
    }
}