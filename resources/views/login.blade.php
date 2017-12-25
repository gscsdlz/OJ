@extends('layout')
@section('title')
    登录与注册
@endsection
@section('main')
<div class="row">
    <div class="col-md-8">
        <img src="{{ URL('image/loginBG.jpg') }}" />
        <h1 class="text-center">今天的你，AC了吗？</h1>
    </div>
    <div class="col-md-3">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#signIn" aria-controls="signIn" role="tab" data-toggle="tab">登录</a>
            </li>
            <li role="presentation">
                <a href="#signUp" aria-controls="signUp" role="tab" data-toggle="tab">注册</a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="signIn">
                <div class="panel panel-default">
                    <div class="panel-body">
                    <form>
                        <div class="form-group">
                            <label for="username">用户名</label>
                            <input type="text" class="form-control" id="username" placeholder="请输入用户名">
                        </div>
                        <div class="form-group">
                            <label for="password">密码</label>
                            <input type="password" class="form-control" id="password" placeholder="请输入密码">
                        </div>
                    </form>
                    </div>
                    <div class="panel-footer text-right">
                        <span class="text-danger" style="float: left" id="loginInfo"></span>
                        <button type="button" class="btn btn-primary" id="login">登录</button>
                        <button type="button" class="btn btn-danger" id="findPass">免密登录</button>
                    </div>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="signUp">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <form>
                            <div class="form-group">
                                <label for="newUsername">用户名</label>
                                <input type="text" class="form-control" id="newUsername" placeholder="仅能使用大小写字母数字或者下划线">
                            </div>
                            <div class="form-group">
                                <label for="nickname">昵称</label>
                                <input type="text" class="form-control" id="nickname" placeholder="请输入昵称推荐 学校-年级-姓名">
                            </div>
                            <div class="form-group">
                                <label for="email">电子邮箱</label>
                                <input type="email" class="form-control" id="email" placeholder="请输入电子邮件用于注册和找回密码">
                            </div>
                            <div class="form-group">
                                <label for="password1">密码</label>
                                <input type="password" class="form-control" id="password1" placeholder="请输入密码">
                            </div>
                            <div class="form-group">
                                <label for="password2">再次输入密码</label>
                                <input type="password" class="form-control" id="password2" placeholder="请再次输入密码">
                            </div>
                            <label>密码强度</label>
                            <div class="progress">
                                <div id="pre" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em;width:0%">
                                    0%
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="panel-footer text-right">
                        <span class="text-danger" style="float: left" id="regInfo"></span>
                        <button type="button" class="btn btn-primary" id="reg">注册</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var token = "{{ csrf_token() }}";
    $(document).ready(function(){
        $("#findPass").hide();

        $("#login").click(function(){

            $(".form-group").removeClass("has-error");

            var username = $("#username").val();
            var password = $("#password").val();

            if(username.length == 0) {
                $("#username").parent().addClass("has-error");
                $("#loginInfo").html("用户名不能为空");
            } else if(password.length == 0) {
                $("#password").parent().addClass("has-error");
                $("#loginInfo").html("密码不能为空");
            } else {
                $.post("{{ URL('user/login') }}", {_token:token, username:username, password:password}, function(data){
                    if(data.status == false) {
                        if(data.info == "vcode error")
                            $("#loginInfo").html("验证码错误");
                        else {
                            $("#loginInfo").html("用户名或者密码错误");
                            $("#password").attr('type', 'text');
                            window.setTimeout('$("#password").attr("type", "password")', 3000);
                            $("#findPass").show();
                        }
                    } else {
                        window.location.reload();
                    }
                })
            }
        })
        $("#findPass").click(function(){
            var username = $("#username").val();
            if(username.length == 0) {
                $("#loginInfo").html("请填写用户名");
            } else {
                $("#loginInfo").html("发送中......");
                $.post("{{ URL('user/findPass') }}", {_token:token, username:username}, function(data){
                    if(data.status == true)
                        $("#loginInfo").html("我们已经给您所填写的邮箱" + data.info + "发送了免密登录链接，请查收！");
                    else
                        $("#loginInfo").html("发送失败");
                })

            }
        })

        $("#reg").click(function(){
            $(".form-group").removeClass('has-error');
            var username = $("#newUsername").val();
            var nickname = $("#nickname").val();
            var password1 = $("#password1").val();
            var password2 = $("#password2").val();
            var email = $("#email").val();

            if(username.length == 0) {
                $("#newUsername").parent().addClass('has-error');
                $("#regInfo").html("用户名不能为空！");
            } else if(nickname.length == 0) {
                $("#nickname").parent().addClass('has-error');
                $("#regInfo").html("昵称不能为空");
            } else if(email.length == 0) {
                $("#email").parent().addClass('has-error');
                $("#regInfo").html("电子邮箱不能为空");
            } else if(password1.length == 0 || password1 != password2) {
                $("#password1").parent().addClass('has-error');
                $("#password2").parent().addClass('has-error');
                $("#regInfo").html("两次密码不匹配")
            } else {
                $("#regInfo").html("注册中......")
                $.post("{{ URL('user/register') }}", {
                    username:username,
                    nickname:nickname,
                    password:password1,
                    email:email,
                    _token:token,
                }, function(data){
                    if(data.status == true) {
                        $("#regInfo").html("邮件发送成功，请注意查收！");
                        $("#reg").html("请稍后重试(30S)");
                        $("#reg").attr('disabled', 'disabled');
                        window.setTimeout('$("#reg").html("注册");$("#reg").removeAttr("disabled");', 30000);
                    } else {
                        $("#regInfo").html(data.info);
                    }
                })
            }
        })

        $("#password1").keyup(function(){
            var pre = 0;
            var pass = $("#password1").val();
            if(pass.length > 10)
                pre += (pass.length - 10) * 10;
            pre = pre > 40 ? 40 :pre;

            if(/[A-Z]+/.test(pass))
                pre += 20;
            if(/[a-z]+/.test(pass))
                pre += 20;
            if(/[0-9]+/.test(pass))
                pre += 20;

            $("#pre").html(pre + "%");
            $("#pre").css('width', pre + "%")
        })
    })
</script>
@endsection