@extends('layout')
@section('title')
    注册激活确认
@endsection
@section('main')
    <div class="row">
        <div class="col-md-8 col-md-offset-2 well">
            @if(!Session::has('user_id'))
                <p class="text-danger text-center">激活失败，token已经过期或不匹配，请重试</p>
            @else
                <p class="text-danger text-center">注册成功，欢迎来到NUC Online Judge请到个人中心完善更多信息</p>
            @endif
        </div>
    </div>

@endsection