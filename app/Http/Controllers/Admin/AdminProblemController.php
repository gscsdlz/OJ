<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/27
 * Time: 15:19
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Model\PointModel;
use App\Model\PointProblemModel;
use App\Model\ProblemModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProblemController extends Controller
{
    public function add(Request $request, $pid = 0)
    {
        $problem = ProblemModel::select('pro_id', 'pro_title', 'time_limit', 'memory_limit', 'author', 'pro_dataIn', 'pro_dataOut')
            ->where('pro_id', $pid)->first();
        if(!is_null($problem)) {
            $lists = AdminDataController::get_list($pid);
        } else {
            $lists = [];
        }
        $points = PointProblemModel::select('point_id')->where('pro_id', $pid)->get();
        $pointLists = PointModel::select('point_id', 'point_name')->get();
        return view('admin.problem_add', [
            'pid' => $pid,
            'menu' => 'problem',
            'problem' => $problem,
            'points' => $points,
            'pointLists' => $pointLists,
            'fileLists' => $lists,
        ]);
    }

    public function get_others(Request $request)
    {
        $pros = ProblemModel::select('pro_descrip', 'pro_in', 'pro_out', 'hint')
            ->where('pro_id', $request->get('pid'))->first();
        return response()->json([
            $pros->toArray()
        ]);
    }

    public function lists(Request $request, $page = 1)
    {
        if($page < 1)
            $page = 1;
        $pms = config('web.ProblemPageMax');
        $lists = ProblemModel::select('pro_id', 'pro_title', 'visible')->where([
            ['pro_id', '>=', 1000 + ($page - 1) * $pms],
            ['pro_id', '<=', 1000 + ($page) * $pms],
        ])->get();
        $mount = ProblemModel::count();
        return view('admin.problem_list',[
            'menu' => 'problem',
            'page' => $page,
            'proLists' => $lists,
            'mount' => (int)(($mount - 1) / $pms + 1),
        ]);
    }

    public function show_visible(Request $request)
    {
        $lists = ProblemModel::select('pro_id', 'pro_title', 'visible')->where('visible', '0')->get();
        return view('admin.hidden_problem_list',[
            'menu' => 'problem',
            'proLists' => $lists,
        ]);
    }


    public function del_problem(Request $request)
    {
        $pid = $request->get('pid');
        $affRow = ProblemModel::destroy($pid);
        return response()->json([
            'status' => $affRow > 0 ? true : false
        ]);
    }

    public function do_visible(Request $request)
    {
        $pid = $request->get('pid');
        $visible = $request->get('visible');

        $affRow = ProblemModel::where('pro_id', $pid)->update(['visible' => $visible]);
        return response()->json([
           'status' => $affRow > 0 ? true : false
        ]);
    }

    public function insert_problem(Request $request)
    {
        $pid = $request->get('pid');
        $pro_title = $request->get('pro_title', '');
        $timel = $request->get('timel', 0);
        $meml = $request->get('meml', 0);
        $author = $request->get('author', '');
        $pro_descrip = $request->get('pro_descrip', '');
        $pro_in = $request->get('pro_in', '');
        $pro_out = $request->get('pro_out', '');
        $pro_hint = $request->get('pro_hint', '');
        $pro_sin = $request->get('pro_sin', '');
        $pro_sout = $request->get('pro_sout', '');
        $points = $request->get('points', []);

        //全部清空 再插入
        PointProblemModel::where('pro_id', $pid)->delete();

        if(strlen($pro_title) == 0 || strlen($pro_descrip) == 0 || strlen($pro_in) == 0 || strlen($pro_out) == 0 || $timel <= 0 || $meml <= 0)
            return response()->json(['status' => false, 'info' => 'empty']);
        if(isset($pid) && $pid > 0) {
            $row = ProblemModel::where('pro_id', $pid)->update([
                'pro_title' => $pro_title,
                'time_limit' => $timel,
                'memory_limit' => $meml,
                'pro_descrip' => $pro_descrip,
                'pro_in' => $pro_in,
                'pro_out' => $pro_out,
                'pro_dataIn' => $pro_sin,
               'pro_dataOut' => $pro_sout,
                'hint' => $pro_hint,
                'author' => $author,
            ]);
            $arr = [];
            foreach ($points as $p) {
                $arr[] = ['pro_id' => $pid, 'point_id' => $p];
            }
            $id = DB::table('point_pro')->insert($arr);

            if ($row > 0 || $id > 0) {
                return response()->json(['status' => true, 'info' => $pid]);
            } else {
                return response()->json(['status' => false, 'info' => 'error']);
            }
        } else {
            $pro = new ProblemModel();
            $pro->pro_title = $pro_title;
            $pro->time_limit = $timel;
            $pro->memory_limit = $meml;
            $pro->pro_descrip = $pro_descrip;
            $pro->pro_in = $pro_in;
            $pro->pro_out = $pro_out;
            $pro->pro_dataIn = $pro_sin;
            $pro->pro_dataOut = $pro_sout;
            $pro->hint = $pro_hint;
            $pro->author = $author;
            $pro->visible = 0;
            $pro->save();
            if ($pro->pro_id > 0) {

                $arr = [];
                foreach ($points as $p) {
                    $arr[] = ['pro_id' => $pro->pro_id, 'point_id' => $p];
                }
                DB::table('point_pro')->insert($arr);

                return response()->json(['status' => true, 'info' => $pro->pro_id]);
            } else {
                return response()->json(['status' => false, 'info' => 'error']);
            }
        }
    }

    public function download(Request $request, $pid, $name)
    {
        $path = '/home/judge/data/'.$pid.'/'.$name;

        if(file_exists($path) && !is_dir($path)) {
            return response()->download($path);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function downloads(Request $request, $pid)
    {
        $path = AdminDataController::zip_files($pid);
        if($path != '')
            return response()->download($path);
        else
            return json_encode()->json(['status' => false]);

    }

    public function del_file(Request $request)
    {
        $pid = $request->get('pid');
        $name = $request->get('name');
        $path = AdminDataController::del_file($pid, $name);
        if($path != '') {
            return response()->json(['status' => true, 'path' => $path]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function undo(Request $request)
    {
        $name = $request->get('name');
        $res = AdminDataController::undo($name);
        if($res === false)
            return response()->json(['status' => false]);
        else
            return response()->json(['status' => true, 'res' => $res]);
    }

    public function upload(Request $request, $pid)
    {
        $uf = $request->file;

        if($uf->isValid()) {
            $allows = ['application/zip', 'text/plain'];
            $extension = $uf->getMimeType();
            if(in_array($extension, $allows)) {
                $uf->move('/home/judge/data/'.$pid, $uf->getClientOriginlName());

            }
        }
        return response()->json(['status' => false]);
    }

}