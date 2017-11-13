<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/27
 * Time: 9:03
 */

namespace App\Http\Controllers\Admin;
use App\Http\Controllers;
use App\Model\ContestModel;
use App\Model\ProblemModel;
use App\Model\StatusModel;
use App\Model\UserModel;

class AdminIndexController extends Controllers\Controller
{
    public function index()
    {
        $pros = ProblemModel::count();
        $users = UserModel::count();
        $submits = StatusModel::count();
        $contests = ContestModel::count();

        return view('admin.index',
            [
                'menu' => 'index' ,
                'pros' => $pros,
                'users' => $users,
                'submits' => $submits,
                'contests' => $contests,
            ]
        );
    }
}