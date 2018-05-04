<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/28
 * Time: 15:42
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Model\TeamModel;
use App\Model\UserModel;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function list_all_team(Request $request)
    {
        $teamLists  = TeamModel::selectRaw('team.team_id, team_name, private, COUNT(user_id) AS mount')
            ->leftJoin('users', 'users.team_id', '=', 'team.team_id')
            ->groupBy('team.team_id')->get();
        return view('admin.team_bind', [
           'menu' => 'user',
            'teamLists' => $teamLists,
        ]);
    }

    public function del_team(Request $request)
    {
        $tid = $request->get('tid');
        $affRow = UserModel::where('team_id', $tid)->update([
            'team_id' => 1
        ]);
        $id = TeamModel::destroy($tid);
        if($id > 0)
            return response()->json([
                'status' => true
            ]);
        else
            return response()->json([
                'status' => false
            ]);
    }

    public function  change_user(Request $request)
    {
        $tid = $request->get('tid');
        $uids = $request->get('users');
        foreach ($uids as $id) {
            UserModel::where('user_id', $id)->update([
                'team_id' => $tid
            ]);
        }
        return response()->json([
            'status' => true
        ]);
    }

    public function add_team(Request $request)
    {
        $private = $request->get('private');
        $tname = $request->get('tname');

        if(TeamModel::where('team_name', 'like',$tname)->count() > 0)
            return response()->json([
                'status' => false
            ]);
        else {
            $team = new TeamModel();
            $team->team_name = $tname;
            $team->private = $private;
            $team->save();
            return response()->json([
                'status' => true
            ]);
        }
    }

    public function change_team(Request $request)
    {
        $tid = $request->get('tid');
        $private = $request->get('private');
        $tname = $request->get('tname');

        $affRow = TeamModel::where('team_id', $tid)->update([
            'private' => $private,
        ]);



        if(TeamModel::where('team_name', 'like',$tname)->count() > 0 && $affRow == 0)
            return response()->json([
                'status' => false
            ]);
        else {
            TeamModel::where('team_id', $tid)->update([
                'team_name' => $tname,
            ]);
            return response()->json([
                'status' => true
            ]);
        }
    }

    public function get_users(Request $request)
    {
        $tid = $request->get('tid');
        $usersLists = UserModel::select('user_id', 'username', 'nickname')->where('team_id', $tid)->get();
        return response()->json(
            [
                'res' => $usersLists
            ]
        );
    }

    public function import_user(Request $request)
    {
        return view('admin.import_user');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 上传的文件格式 说明如下
     * 用户名 昵称 密码  队伍编号 座位号
     * 其中队伍编号需要提前在队伍管理中添加 并并获取ID，座位号可以使用空字符串，但是必须有
     * 密码使用明文密码：例如123456
     * 字段之间的分割使用“ ”空格或者“,”逗号
     */
    public function upload_user(Request $request)
    {
        $files = $request->file('file');
        $delimiter = $request->get('arg');

        if ($files->getClientMimeType() != 'application/vnd.ms-excel')
            return response()->json( [ 'status' => false, 'info' => '不是标准CSV文件']);

        $fp = $files->openFile('r');
        $res = [];
        $k = 0;
        while(!$fp->eof()) {
            $str = $fp->fgets();
            if($str == "")
                continue;
            $k++;
            $arr = explode($delimiter,$str);

            if(count($arr) != 5) {
               return response()->json( [ 'status' => false, 'info' => '第'.$k++.'行字段数目不合法，当前为'.count($arr)]);
            }

            foreach ($arr as &$row)
            {
                $row = trim($row);
                $row = trim($row,'"');
            }
            unset($row);
            $res[] = $arr;
        }
        if(count($res) == 0)
            return response()->json( [ 'status' => false, 'info' => 'CSV文件为空']);
        //var_dump($res);
        return response()->json([ 'status' => true, 'res' => $res]);
    }

    public function do_import(Request $request)
    {
        $data = $request->get('data');


        $teamArr = [];
        foreach($data as $row) {
         //   var_dump($row);
            $teamArr[$row[3]] = 1;
        }
        foreach($teamArr as $key => $val) {
            $team = TeamModel::select('team_id')->where('team_name', $key)->first();
            if (is_null($team)) {
                $t = new TeamModel();
                $t->team_name = $key;
                $t->save();
                $teamArr[$key] = $t->team_id;
            } else {
                $teamArr[$key] = $team->team_id;
            }
        }

        foreach ($data as $row) {
            $users = new UserModel();
            $users->username = $row[0];
            $users->nickname = $row[1];
            $users->password = sha1($row[2]);
            $users->team_id = $teamArr[$row[3]];
            $users->seat = $row[4];
            $users->activate = 0;
            $users->save();
        }

        return response()->json([
            'status' => true
        ]);
    }

}