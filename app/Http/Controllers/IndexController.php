<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/26
 * Time: 11:14
 */

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{
    public function help()
    {
        return view('help', ['menu' => 'index@help']);
    }

    public function index()
    {
        return view('index');
    }

    public function about()
    {
        return view('about', ['menu' => 'index@about']);
    }

    public function login()
    {
        if(Session::has('user_id'))
            return response()->redirectTo('index');
        else
            return view('login');
    }

    public function test()
    {
        dd(preg_match('/^[A-Za-z0-9_]+$/', '-1234'));
    }
}