@extends('layout')
@section('title')
    账号绑定
@endsection
@section('main')
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            @if(isset($user['ret']) && $user['ret'] == 0)
            <div class="panel panel-success">
                <div class="panel-heading">
                    QQ账户绑定
                </div>
                <div class="panel-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">昵称</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $user['nickname'] }}" id="nickname" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">头像</label>
                            <div class="col-sm-8">
                                <img class="img-circle" src="{{ $user['figureurl_qq_1'] }}" alt="40x40"/>
                                <p class="text-danger">头像仅提供预览功能 修改请在个人中心修改</p>
                            </div>
                        </div>
                        <hr/>

                        <div class="form-group">
                            <label class="control-label col-sm-2">用户名</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="" id="username" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2">密码</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" value="" id="password" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-2">
                                <p class="text-danger">如果您没有账户，输入用户名和密码会注册并绑定QQ</p>
                                <p class="text-danger">如果您已有账户，输入用户名和密码会直接绑定QQ</p>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer text-right">
                    <span class="text-danger" style="float: left" id="bindInfo"></span>
                    <button class="btn btn-primary" type="button" id="bind">绑定 / 注册</button>
                </div>
            </div>
            @else
            <h1 class="text-center text-danger">{{ $user['msg'] }}</h1>
            @endif
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $("#bind").click(function(){
                var nickname = $("#nickname").val();
                var username = $("#username").val();
                var password = $("#password").val();

                if(username.length == 0) {
                    $("#bindInfo").html("用户名不能为空");
                    $("#username").parent().parent().addClass("has-error");
                } else if(password.length == 0) {
                    $("#bindInfo").html("密码不能为空");
                    $("#password").parent().parent().addClass("has-error");
                } else {
                    $("#bindInfo").html("请稍后，正在创建用户......")
                    $.post("{{ URL('user/qq_bind') }}", {
                        nickname:nickname, username:username,
                        password:password, _token:"{{ csrf_token() }}",
                    }, function(data){
                        if(data.status == true) {
                            window.location.href= "{{ URL('/index') }}";
                        } else {
                            $("#bindInfo").html(data.info);
                        }
                    })
                }
            })
        })
    </script>
@endsection