@extends('layout')
@section('title')
    免密登录确认
@endsection
@section('main')
    <div class="row">
        <div class="col-md-8 col-md-offset-2 well">
            @if(!Session::has('user_id'))
                <p class="text-danger text-center">免密登录失败，token已经过期或不匹配，请重试</p>
            @else
                <p class="text-danger text-center">免密登录成功，请到个人中心自行更改密码</p>
            @endif
        </div>
    </div>

@endsection