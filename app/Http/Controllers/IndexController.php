<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/26
 * Time: 11:14
 */

namespace App\Http\Controllers;

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
}