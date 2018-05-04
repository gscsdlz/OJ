<?php

namespace App\Http\Controllers;

use App\Model\AnswerModel;
use App\Model\ContestModel;
use App\Model\ContestProblemModel;
use App\Model\ProblemModel;
use App\Model\QuestionModel;
use App\Model\StatusModel;
use App\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;

class ContestController extends Controller
{
    public function list_all(Request $request, $old = false)
    {
        if($old == true)
            $contest_list = ContestModel::orderBy('c_stime', 'DESC')->get();
        else
            $contest_list = ContestModel::orderBy('c_stime', 'DESC')->where('c_etime', '>=', time())->get();
        return view('contest_list', [
            'menu' => 'contest@list',
            'lists' => $contest_list,
            'old' => $old
        ]);
    }

    public function show(Request $request, $cid, $pid = 0)
    {
        /**
         * 这里暂时无法创建关联 即比赛模型和题目模型，绑定后，无法自动获取pro_id
         */
        $contest = ContestModel::where('contest_id', $cid)->first();
        $proLists = $contest->problems()->rightJoin('problem', 'problem.pro_id', '=' ,'contest_pro.pro_id')
            ->select('problem.pro_id', 'inner_id', 'max_score', 'pro_title')->orderBy('inner_id')->get();
        if(count($proLists) > 0) {
            if ($pid == 0) {
                $pid = $proLists->first()->inner_id;
            }

            foreach ($proLists as &$pros) {
                $pros->set_ac_num($cid);
                $pros->set_ac_status(Session::get('user_id'), $cid);
            }
            unset($pros);


            $pro = ContestProblemModel::leftJoin('problem', 'contest_pro.pro_id', '=', 'problem.pro_id')
                ->where([
                    ['contest_id', $cid],
                    ['inner_id', $pid]
                ])->first();
            $pro->set_ac_num($cid);
            $pro->set_ac_status(Session::get('user_id'), $cid);
            return view('contest_pro', [
                'menu' => 'contest@show',
                'cid' => $cid,
                'pid' => $pid,
                'pro' => $pro,
                'contest' => $contest,
                'proLists' => $proLists,
            ]);
        } else {
            return view('contest_pro', [
                'menu' => 'contest@show',
                'cid' => $cid,
                'pid' => $pid,
                'contest' => $contest,
                'proLists' => $proLists,
            ]);
        }
    }

    public function rank(Request $request, $cid, $page = 0, $group = -1)
    {
        $tmp = Redis::get('contest_rank_cache:'.$cid);
        $ttl = null;
        if(is_null($tmp)) {

            $cranks = [];
            $this->get_all_status($cid, $cranks);
            $contest = ContestModel::where('contest_id', $cid)->first();
            $inner_ids = ContestProblemModel::select('inner_id')->where('contest_id', $cid)->orderBy('inner_id')->get();

            if ($contest->oi == 1)
                $tmp = $this->sort_with_oi($contest->c_stime, $cranks);
            else
                $tmp = $this->sort_without_oi($contest->c_stime, $inner_ids, $cranks);
            Redis::set('contest_rank_cache:' . $cid, json_encode($tmp));
            Redis::expire('contest_rank_cache:' . $cid, 100);
        } else {

            $contest = ContestModel::where('contest_id', $cid)->first();
            $inner_ids = ContestProblemModel::select('inner_id')->where('contest_id', $cid)->orderBy('inner_id')->get();
            $tmp = json_decode($tmp, true);
            $ttl = Redis::ttl('contest_rank_cache:'.$cid);
            //dd($ttl);
        }
        $args = array();
        $pstart = $page * 50 + 1;
        $pend = ($page + 1) * 50;
        $p = 1;
        $rank = 1;
        $t = 0;
        $groups = array();
        foreach ($tmp as $key) {
            $groups[] = $key[3];
            if ($group == -1) {
                if ($p >= $pstart && $p <= $pend) {
                    $args[$t] = $key;
                    $args[$t++][5] = $rank;
                }
                $p++;
            } else if ($group == $key[3]) {
                if ($p >= $pstart && $p <= $pend) {
                    $args[$t] = $key;
                    $args[$t++][5] = $rank;
                }
                $p++;
            }
            $rank++;
        }

        return view('contest_rank', [
            'menu' => 'contest@rank',
            'contest' => $contest,
            'cid' => $cid,
            'ttl' => $ttl,
            'pageN' => $page,
            'pageT' => (int)(($p + 1) / 50) + 1,
            'teams' => array_unique($groups),
            'team' => $group,
            'ranks' => $args,
            'ids' => array_column($inner_ids->toArray(), 'inner_id'),
        ]);
        
    }

    public function del_answer(Request $request)
    {
        $id = $request->get('id');
        $flag = $request->get('all');

        if(Session::has('user_id') && Session::get('privilege') == 1) {
            if($flag == false) {
                AnswerModel::where('answer_id', $id)->delete();
            } else {
                AnswerModel::where('question_id', $id)->delete();
                QuestionModel::where('question_id', $id)->delete();
            }
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);
    }

    private function get_all_status($cid, &$cranks)
    {
        $pros = ContestProblemModel::select('inner_id', 'pro_id')->where('contest_id', $cid)->get()->toArray();
        $pros = array_combine(array_column($pros, 'pro_id'), array_column($pros, 'inner_id'));
        
        $status = StatusModel::select('user_id', 'pro_id', 'status', 'submit_time', 'score')->where('contest_id', $cid)->get();
        if(count($status) > 0) {
            foreach($status as $s) {
                $s->pro_id = $pros[$s->pro_id]; //执行映射
                if(!isset($cranks[$s->user_id])) {
                    $cranks[$s->user_id] = [];
                }
                if(!isset($cranks[$s->user_id][$s->pro_id])) {
                    $cranks[$s->user_id][$s->pro_id] = [];
                }
                $cranks[$s->user_id][$s->pro_id][] = [$s->status, $s->submit_time, $s->score];
            }
        }
    }

    /**
     * 标准ACM比赛下，排名排序函数
     */
    private function sort_without_oi($c_stime, $inner_ids, &$cranks){
        /**
         * $contestrank 层次说明
         * 第一层 用户ID
         * 第二层 当前用户所有题目
         * 第三层 该用户当前题目的提交情况 按时间排序
         * 第四层 该题目状态。该题目提交时间
         * 过程描述。遍历第三层 确定第一次正确提交前有多少次错误
         */
        $tmp = array();
        $fb = array();
        foreach($inner_ids as $row) {
            $fb [$row->inner_id] = array(
                0,
                0
            );
        }
        if ($c_stime && count($cranks) != 0) {

            foreach ($cranks as $key => $users) {
                $tmp [$key] = array();
                $total_time = 0; // 总秒数
                $total_ac = 0; // 总正确次数
                foreach ($users as $key2 => $pro) { // 需要修改
                    $tmp [$key] [$key2] = array();
                    $len = count($pro);
                    $pro_time = 0; // 单个题目总秒数
                    $pro_wa = 0; // 单个题目总错误次数
                    $pro_ac = false;
                    foreach ($pro as $status) {
                        if ($status [0] != 4) {
                            $pro_wa++;
                        } else { // 一旦通过 之后的提交都不在计算
                            if ($fb [$key2] [0] == 0 || $fb [$key2] [0] > ( int )$status [1]) {
                                $fb [$key2] [0] = ( int )$status [1];
                                $fb [$key2] [1] = $key;
                            }
                            $pro_ac = true;
                            $pro_time += ( int )$status [1] - $c_stime;
                            break;
                        }
                    }
                    /**
                     * 说明
                     * time = 0， wa = n 错误了n次 仍然为通过
                     * time = n，wa = m 再经过m次错误以后，通过题目 m可以为0
                     */
                    if (!$pro_ac)
                        $tmp [$key] [$key2] = array(
                            0,
                            $pro_wa
                        );
                    else {
                        $total_ac++;
                        $tmp [$key] [$key2] = array(
                            $pro_time,
                            $pro_wa
                        );
                        $total_time += $pro_time + $pro_wa * 20 * 60; // 加上罚时
                    }
                } // $pro
                $tmp [$key] [0] = $total_time;
                $tmp [$key] [1] = $total_ac;
                $userinfo = $this->get_userinfo($key);
                $tmp [$key] [2] = $userinfo [0];
                $tmp [$key] [3] = $userinfo [2];
                $tmp [$key] [4] = $userinfo [1];
            } // users
        }

        unset ($cranks);


        uasort($tmp, function($a, $b)
        {
            if ($a [1] == $b [1]) {
                if ($a [1] == 0)
                    return $a [0] > $b [0] ? -1 : 1;
                else
                    return $a [0] > $b [0] ? 1 : -1;
            } else {
                return $a [1] < $b [1] ? 1 : -1;
            }
        });
        foreach ($fb as $key => $value) {
            if ($value [0] > 0)
                $tmp [$value [1]] [$key] [] = 1;//根据fb数组设置tmp数组中的状态值
        }
        return $tmp;
    }

    /**
     * OI模式下，排名排序函数
     */
    private function sort_with_oi($c_stime, &$cranks){
        $tmp = array();
        /*
         * 没有罚时 按照分数排序
         */
        if ($c_stime && count($cranks) != 0) {
            foreach ($cranks as $key => $users) {
                $tmp [$key] = array();
                $total_score = 0; //总分数
                $total_submit = 0; // 总提交次数
                foreach ($users as $key2 => $pro) {
                    $tmp [$key] [$key2] = array();
                    $pro_score = 0; // 单个题目分数
                    $pro_submit = count($pro); // 单个题目总提交次数
                    $pro_ac = false;
                    foreach ($pro as $status) {
                        $pro_score = max($pro_score, $status[2]);
                        if($status[0] == 4)
                            $pro_ac = true;
                    }
                    $tmp[$key][$key2] = array(
                        $pro_score,
                        $pro_submit,
                        $pro_ac
                    );
                    $total_score += $pro_score;
                    $total_submit += $pro_submit;
                } // $pro
                $tmp [$key] [0] = $total_score;
                $tmp [$key] [1] = $total_submit;
                $userinfo = $this->get_userinfo($key);
                $tmp [$key] [2] = $userinfo [0];
                $tmp [$key] [3] = $userinfo [2];
                $tmp [$key] [4] = $userinfo [1];
            } // users
        }

        unset ($cranks);


        uasort($tmp, function($a, $b)
        {
            if ($a [0] == $b [0]) {
                return $a [1] > $b [1] ? 1 : -1;
            } else {
                return $a [0] < $b [0] ? 1 : -1;
            }
        });

        return $tmp;
    }

    private function get_userinfo($uid)
    {
        $u = UserModel::select('username', 'nickname', 'team_name')->leftJoin('team', 'team.team_id', '=', 'users.team_id')->where('user_id', $uid)->first();
        return [$u->username, $u->nickname, $u->team_name];
    }


    public function ask_show(Request $request, $cid, $page = 1)
    {
        $problems = ContestProblemModel::select('inner_id')->where('contest_id', $cid)->orderBy('inner_id')->get();
        $questionLists = QuestionModel::select('question_id', 'topic_question', 'username','ask_time','pro_id')->leftJoin('users', 'users.user_id','=' ,'question.user_id')->where('contest_id', $cid)->get();
        $contest = ContestModel::select('contest_name')->where('contest_id', $cid)->first();
        return view('question', [
            'contest' => $contest,
            'menu' => 'contest@ask',
            'cid' => $cid,
            'lists' => $questionLists,
            'problems' => $problems
        ]);
    }

    public function add_question(Request $request)
    {
        $cid = $request->get('cid');
        $str = $request->get('str');
        if(strlen($str) == 0)
            return response()->json([
                'status' => false
            ]);
        $pro_id = $request->get('pro_id');

        $q = new QuestionModel();
        $q->topic_question = htmlspecialchars($str);
        $q->contest_id = $cid;
        $q->user_id = Session::get('user_id');
        $q->ask_time = time();
        $q->pro_id = $pro_id;
        $q->urgent = Session::get('privilege') == 1 ? 1 : 0;
        $q->save();
        return response()->json([
            'status' => true
        ]);
    }

    public function add_answer(Request $request)
    {
        $str = $request->get('str');
        $qid = $request->get('question_id');
        if(strlen($str) == 0)
            return response()->json([
                'status' => false
            ]);
        $a = new AnswerModel();
        $a->question_id = $qid;
        $a->topic_answer = htmlspecialchars($str);
        $a->user_id = Session::get('user_id');
        $a->reply_time = time();
        $a->save();

        return response()->json([
            'status' => true
        ]);
    }


    public function get_answer(Request $request)
    {
        $cid = $request->get('cid');
        $qid = $request->get('qid');

        $lists = AnswerModel::select('answer_id', 'topic_answer', 'username', 'headerpath', 'reply_time')
            ->leftJoin('users', 'answer.user_id', '=', 'users.user_id')
            ->where('question_id', $qid)
            ->get();
        foreach ($lists as &$row)
            $row->reply_time = date('Y-m-d H:i:s', $row->reply_time);
        unset($row);
        return response()->json([
            'res' => $lists,
        ]);
    }

    public function get_URG_info(Request $request)
    {
        $cid = $request->get('cid');
        $infos = QuestionModel::select('question_id', 'topic_question')->where([
            ['urgent', '1'],
            ['contest_id', $cid],
        ])->get();

        return response()->json(
            ['status' => true,
            'infos' => $infos->toArray()]
        );
    }

    /**
     * 导出比赛排名数据
     * @param Request $request
     */
    public function exportResult(Request $request, $cid)
    {
        $tmp = Redis::get('contest_rank_cache:'.$cid);
        $ttl = null;
        if(is_null($tmp)) {

            $cranks = [];
            $this->get_all_status($cid, $cranks);
            $contest = ContestModel::where('contest_id', $cid)->first();
            $inner_ids = ContestProblemModel::select('inner_id')->where('contest_id', $cid)->orderBy('inner_id')->get();

            if ($contest->oi == 1)
                $tmp = $this->sort_with_oi($contest->c_stime, $cranks);
            else
                $tmp = $this->sort_without_oi($contest->c_stime, $inner_ids, $cranks);
            Redis::set('contest_rank_cache:' . $cid, json_encode($tmp));
            Redis::expire('contest_rank_cache:' . $cid, 100);
        } else {

            $contest = ContestModel::where('contest_id', $cid)->first();
            $inner_ids = ContestProblemModel::select('inner_id')->where('contest_id', $cid)->orderBy('inner_id')->get();
            $tmp = json_decode($tmp, true);
            $ttl = Redis::ttl('contest_rank_cache:'.$cid);
        }
        $excel = new \PHPExcel();
        $excel->getProperties()
            ->setCreator("NUC Online Judge")
            ->setTitle("CONTEST RANK")
            ->setSubject("CONTEST RANK");

        $sheet = $excel->setActiveSheetIndex(0);

        $sheet->setCellValue("A1", $contest->contest_name. '比赛排名')
            ->setCellValue("A2", "排名")
            ->setCellValue("B2", "用户名")
            ->setCellValue("C2", "昵称")
            ->setCellValue("D2", "所在小组")
            ->setCellValue("E2", "通过题目数");

        $i = 0;
        foreach ($inner_ids as $row) {
            $coordiante = chr(ord("F") + $i)."2";
            $sheet->setCellValue($coordiante, $row->inner_id);
            $i++;
        }
        $sheet->mergeCells("A1:P1");

        $i = 3;

        foreach ($tmp as $row) {
            $sheet->setCellValue("A" . $i, $i-2)
                ->setCellValue("B" . $i, $row[2])
                ->setCellValue("C" . $i, $row[4])
                ->setCellValue("D" . $i, $row[3])
                ->setCellValue("E" . $i, $row[1]);

            $k = 0;
            foreach ($inner_ids as $c) {
                $cel = $c->inner_id;
                $coordiante = chr(ord("F") + $k).$i;

                if (isset($row[$cel])) {
                   if ($row[$cel][0])
                        $sheet->setCellValue($coordiante, "√");
                    else
                        $sheet->setCellValue($coordiante, "×");
                } else {
                    $sheet->setCellValue($coordiante, "-");
                }
                $k++;
            }
            $i++;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="比赛排名表.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');
    }
}