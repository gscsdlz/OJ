<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/10/25
 * Time: 16:32
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\PointModel;
use App\Model\PointProblemModel;
use Illuminate\Http\Request;

class AdminPointController extends Controller
{
    public function list_all(Request $request)
    {
        $points = PointModel::selectRaw('point.*, COUNT(pro_id) AS pros')->leftJoin('point_pro', 'point_pro.point_id', '=' , 'point.point_id')->groupBy('point.point_id')->get();
        return view('admin.point',
            [
                'lists' => $points,
                'menu' => 'problem'
            ]);
    }
    
    public function do_edit(Request $request) 
    {
        $name = $request->get('name');
        $pid = $request->get('pid');
        
        if(PointModel::where([['point_id','!=' , $pid],['point_name', $name]])->count() == 0) {
            $affRow = PointModel::where('point_id', $pid)->update(['point_name' => $name]);
            if($affRow == 1)
                return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);
    }

    public function del(Request $request)
    {
        $pid = $request->get('pid');
        PointModel::destroy($pid);
        PointProblemModel::where('point_id', $pid)->delete();
        return response()->json(['status' => true]);
    }

    public function do_add(Request $request)
    {
        $name = $request->get('name');
        if(PointModel::where('point_name', $name)->count() == 0) {
            $pkey = PointModel::insertGetId(['point_name' => $name]);
            if($pkey > 0)
                return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);
    }
}