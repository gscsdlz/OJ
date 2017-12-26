<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|09-yui
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/


Route::any('OJ/test', 'IndexController@test');

Route::group(['middleware' => ['web'], 'prefix' => 'OJ'], function () {

    Route::get('login/qq', 'UserController@qqLogin');
    Route::get('qq_login', 'UserController@qqLogin');
    Route::get('login','IndexController@login');
    Route::get('activate', 'UserController@do_register');

    Route::post('user/findPass', 'UserController@findPass');
    Route::post('user/qq_bind', 'UserController@qq_bind');
    Route::get('user/resetPass', 'UserController@resetPass');

    Route::get('/index', 'IndexController@index');
    Route::post('user/uploadHeader', 'UserController@upload');
    Route::get('problem/page/{page?}', 'ProblemController@page');
    Route::get('problem/show/{pid}', 'ProblemController@show');
    Route::get('problem/search', 'ProblemController@search');

    Route::get('status', 'StatusController@status');

    Route::get('code/show/{sid}/{cid?}', 'CodeController@show')->middleware('codeCheck')->where(['sid'=>'[0-9]+', 'cid'=>'[0-9]*']);
    Route::get('code/compiler_show/{sid}/{cid?}', 'CodeController@compiler_show')->middleware('codeCheck')->where(['sid'=>'[0-9]+', 'cid'=>'[0-9]*']);

    Route::get('user/show/{username}', 'UserController@show');
    Route::post('user/login', 'UserController@login');
    Route::post('user/logout', 'UserController@logout');
    Route::post('user/register', 'UserController@register');
    Route::post('user/get_ip_addr', 'UserController@ip_addr_map');
    Route::any('rank/nearby/{username}', 'RankController@nearByUser');

    Route::get("rank/show/{page?}", 'RankController@show');

    Route::any('vcode/{rand?}', 'UserController@vcode');

    Route::get('/contest/list/{old?}', 'ContestController@list_all');
    Route::get('/contest/rank/{cid}/{page?}/{group?}', 'ContestController@rank')->where(['cid'=>'[0-9]+', 'page'=>'[0-9]*']);
    Route::get('/contest/show/{cid}/{pro_id?}', 'ContestController@show')->where(['cid' => '[0-9]+', 'pro_id' => '[0-9]*'])->middleware('contestPrivilegeCheck');
    Route::get('index/help', 'IndexController@help');
    Route::get('index/about', 'IndexController@about');
    Route::post('problem/get_statistics', 'ProblemController@get_statistics');
    Route::get('/contest/ask/{cid}', 'ContestController@ask_show')->where(['cid' => '[0-9]+']);
    Route::post('contest/add_question', 'ContestController@add_question');
    Route::post('contest/add_answer', 'ContestController@add_answer');
    Route::post('contest/del_answer', 'ContestController@del_answer');

    Route::post('submit', 'CodeController@submit');
    Route::post('contest/get_URG_info', 'ContestController@get_URG_info')->where(['cid' => '[0-9]+']);
    Route::post('contest/get_answer', 'ContestController@get_answer');
    Route::post('user/update', 'UserController@update');
});

Route::group(['middleware' => ['admin'], 'prefix' => 'OJ/admin'], function(){
    Route::get('/', 'Admin\AdminIndexController@index');
    Route::get('/problem/add/{pid?}', 'Admin\AdminProblemController@add');
    Route::get('/problem/list/{page?}', 'Admin\AdminProblemController@lists')->where(['page' => '[0-9]*']);
    Route::post('problem/do_visible','Admin\AdminProblemController@do_visible');
    Route::post('problem/del_pro','Admin\AdminProblemController@del_problem');
    Route::get('problem/show_visible', 'Admin\AdminProblemController@show_visible');
    Route::post('problem/insert', 'Admin\AdminProblemController@insert_problem');
    Route::post('problem/get_others', 'Admin\AdminProblemController@get_others');

    Route::get('team/show/{pri?}', 'Admin\AdminUserController@list_all_team')->where(['pri' => '[0|1]{0,1}']);
    Route::post('user/get_users_byTeam', 'Admin\AdminUserController@get_users');
    Route::post('team/del_team', 'Admin\AdminUserController@del_team');
    Route::post('team/change_user', 'Admin\AdminUserController@change_user');
    Route::post('team/add_team', 'Admin\AdminUserController@add_team');
    Route::post('team/change_team', 'Admin\AdminUserController@change_team');

    Route::get('user/import', 'Admin\AdminUserController@import_user');
    Route::post('user/upload_user', 'Admin\AdminUserController@upload_user');
    Route::post('user/do_import', 'Admin\AdminUserController@do_import');

    Route::get('point/edit', 'Admin\AdminPointController@list_all');
    Route::post('point/do_edit', 'Admin\AdminPointController@do_edit');
    Route::post('point/del', 'Admin\AdminPointController@del');
    Route::post('point/do_add', 'Admin\AdminPointController@do_add');
    
    Route::get('contest/list', 'Admin\AdminContestController@lists');
    Route::post('contest/del', 'Admin\AdminContestController@del');
    Route::get('contest/edit/{cid?}', 'Admin\AdminContestController@edit')->where(['cid' => '[0-9]*']);
    Route::post('contest/save', 'Admin\AdminContestController@save');
    Route::post('contest/pro_check', 'Admin\AdminContestController@pro_check');
    
    Route::get('balloon','Admin\AdminContestController@balloon');
    Route::post('get_balloon', 'Admin\AdminContestController@get_balloon');
    Route::post('send_balloon', 'Admin\AdminContestController@send_balloon');

    Route::get('file/download/{pid}/{name}', 'Admin\AdminProblemController@download');
    Route::get('file/downloads/{pid}', 'Admin\AdminProblemController@downloads');
    Route::post('file/del', 'Admin\AdminProblemController@del_file');
    Route::post('file/undo', 'Admin\AdminProblemController@undo');
    Route::post('file/upload/{pid}', 'Admin\AdminProblemController@upload');
});



