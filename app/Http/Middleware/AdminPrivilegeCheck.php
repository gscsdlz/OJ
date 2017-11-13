<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/9/27
 * Time: 15:22
 */

namespace App\Http\Middleware;


use Illuminate\Support\Facades\Session;
use Closure;

class AdminPrivilegeCheck
{
    public function handle($request, Closure $next)
    {
        $pri = Session::get('privilege', null);
        if(is_null($pri) || $pri != 1) {
            return response(view('404'), 404);
        } else {
            return $next($request);
        }
    }
}