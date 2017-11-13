<?php
/**
 * Created by PhpStorm.
 * User: john
 * Date: 2017/8/1
 * Time: 8:48
 */

namespace App\Http\Middleware;

use App\Model\StatusModel;
use Closure;
use Illuminate\Support\Facades\Session;

class CodePrivilegeCheck
{
    public function handle($request, Closure $next, $sid = null)
    {
        $pri = Session::get('privilege', null);

        if(is_null($pri)) {
            return response(view('404'), 404);
        } else if($pri == 1){
            return $next($request);
        } else {
            $sid = $request->route()->sid;
            $status = StatusModel::where([
                ['submit_id', $sid],
                ['user_id', Session::get('user_id')]
            ])->count();
            if($status == 1)
                return $next($request);
            else
                return response(view('404'), 404);
        }
    }
}