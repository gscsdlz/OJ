<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed"
                    data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span> <span
                        class="icon-bar"></span> <span class="icon-bar"></span> <span
                        class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Welcome to NUC Online Judge</a>
        </div>
        <div class="collapse navbar-collapse"
             id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li @if(isset($menu) && $menu == 'index') class="active" @endif><a href="{{ URL('/index') }}">首页</a></li>
                <li @if(isset($menu) && $menu == 'problem') class="active" @endif><a href="{{ URL('problem/page') }}">题目</a></li>
                <li @if(isset($menu) && $menu == 'status') class="active" @endif><a href="{{ URL('status') }}">状态</a></li>
                <li @if(isset($menu) && $menu == 'rank') class="active" @endif><a href="{{ URL('rank/show/1') }}">排名</a></li>
                <li @if(isset($menu) && $menu == 'contest@list') class="active" @endif><a href="{{ URL('contest/list') }}">比赛</a></li>
                <li @if(isset($menu) && $menu == 'index@help') class="active" @endif><a href="{{ URL('/index/help') }}">帮助</a></li>
                <li @if(isset($menu) && $menu == 'index@about') class="active" @endif><a href="{{ URL('/index/about') }}">关于我们</a></li>
                <li><a href="/gitlab">GitLab</a></li>
            </ul>
            <form class="navbar-form navbar-left" role="search" method="get" action="{{ URL("problem/search") }}">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="输入题目或者来源信息	" name="key" value="{{ old('key') }}">
                    {{ csrf_field() }}
                </div>
                <button type="submit" class="btn btn-default">搜索</button>
            </form>
            <ul class="nav navbar-nav navbar-right">
            @if(Session::has('user_id'))
                <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" id="realname">{{ Session::get('username') }}<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="{{ URL("/user/show/".Session::get('username')) }}">个人中心</a></li>
                        @if(Session::get('privilege') == 1)
                            <li><a href="{{ URL('admin/') }}">后台管理</a></li>
                        @endif
                        <li class="divider"></li>
                        <li id="logout"><a href="#">退出登录</a></li>
                    </ul>
                </li>
            @else
                <li data-toggle="modal" data-target="#signModal" id="signButton"><a href="#">登录</a></li>
                <li data-toggle="modal" data-target="#regModal" id="regButton"><a href="#">注册</a></li>
                @endif
            </ul>

        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
<hr/>
<hr/>
@if(!Session::has('user_id'))
<div class="modal fade" id="signModal" tabindex="-1" role="dialog"
     aria-labelledby="signModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h2 class="modal-title">登录</h2>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form">
                    <div class="form-group text-center text-danger">
                        <label id="loginError" class="control-label ">用户名不存在或密码错误，请重试</label>
                    </div>
                    <div class="form-group">
                        <label for="inputUsername" class="col-sm-2 control-label">用户名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="inputUsername"
                                   name="username" placeholder="请输入用户名">
                        </div>
                        <label id="findPassEmptyError" for="inputUsername" class="col-sm-12 control-label text-danger">请输入需要找回密码的账户名，我们会给您填写的邮箱发送一份重置密码的邮件！</label>
                        <label id="findPassError" for="inputUsername" class="col-sm-12 control-label text-danger">邮件发送失败，请检查用户名</label>
                        <label id="findPassSuccess" for="inputUsername" class="col-sm-12 control-label text-success">邮件发送成功，请注意查收</label>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class="col-sm-2 control-label">密码</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="inputPassword"
                                   name="password" placeholder="请输入密码">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword" class="col-sm-2 control-label">验证码</label>
                        <div class="col-sm-8" id="loginOption">
                            <label id="problem" class="class-label"></label>
                            <div class="radio"><label><input type="radio" name="loginVCode" value=""><span></span></label></div>
                            <div class="radio"><label><input type="radio" name="loginVCode" value=""><span></span></label></div>
                            <div class="radio"><label><input type="radio" name="loginVCode" value=""><span></span></label></div>
                            <div class="radio"><label><input type="radio" name="loginVCode" value=""><span></span></label></div>
                        </div>
                        <label id="lvcodeEmptyError" class="control-label text-danger">答案不能为空</label>
                        <label id="lvcodeError" class="control-label text-danger">答案错误</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-danger" id="findPass">忘记密码</button>
                <button type="button" class="btn btn-primary" id="sign">登录</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="regModal" tabindex="-1" role="dialog"
     aria-labelledby="regModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h2 class="modal-title">注册</h2>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="Username" class="col-sm-2 control-label">用户名</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="Username"
                                   name="newUsername" placeholder="请勿使用中文"> <label
                                    id="nameEmptyError" class="control-label text-danger">用户名不能为空！</label>
                            <label id="nameError" class="control-label text-danger">用户名已经注册过了！</label>
                            <label id="nameRegError" class="control-label text-danger">用户名格式不正确</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nickname" class="col-sm-2 control-label">昵称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="Nickname"
                                   name="newNickname" placeholder="请输入昵称推荐 学校-年级-姓名">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-2 control-label">密码</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="Password"
                                   name="newPassword" placeholder="请输入密码"> <label
                                    id="passwordEmptyError" class="control-label text-danger">密码不能为空</label>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password2" class="col-sm-2 control-label">确认密码</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="Password2"
                                   name="newPassword2" placeholder="请再次输入密码"> <label
                                    id="passwordError" class="control-label text-danger">两次输入的密码不一致</label>

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">电子邮箱</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" id="Email" name="email"
                                   placeholder="请填写常用电子邮件"> <label id="emailEmptyError"
                                                                   class="control-label text-danger">电子邮箱不能为空！</label> <label
                                    id="emailError" class="control-label text-danger">该电子邮箱已经被注册过了！</label>
                            <label id="emailRegError" class="control-label text-danger">电子邮箱格式填写错误！</label>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="reg">注册</button>
            </div>
        </div>
    </div>
</div>
@endif
<script type="text/javascript">

    @if(!Session::has('user_id'))
    function refresh_vcode() {
        var randId = new Date().getTime();

        $.post("{{ URL('vcode') }}/" + randId, {
            _token:"{{ csrf_token() }}"
        }, function(data){
            var arg = eval(data);
            if(arg['status'] == true) {
                $("#problem").html(arg['problem']);
                for(var i = 0; i < arg['options'].length; ++i) {
                    $("#loginOption").children().eq(i + 1).children().eq(0).children().eq(0).val(i);
                    $("#loginOption").children().eq(i + 1).children().eq(0).children().eq(1).html(arg['options'][i]);
                }
            }
        })
    }

    function login(){
        $("#loginError").hide();
        $("#lvcodeEmptyError").hide();
        $("#lvcodeError").hide();
        var username = $("#inputUsername").val();
        var password = $("#inputPassword").val();
        var vcode = $("input:radio[name='loginVCode']:checked").val() ;
        if(vcode.length == 0)
            $("#lvcodeEmptyError").show();
        else
            $.post("{{ URL('user/login') }}", {
                username : username,
                password : password,
                _token:"{{ csrf_token() }}",
                vcode : vcode
            }, function(data) {
                var arr = eval(data);
                if (arr['status'] == false && arr['info'] == 'error vcode') {
                    $("#lvcodeError").show();
                    refresh_vcode();

                } else if(arr['status'] == true){
                    window.location.reload();
                } else {
                    refresh_vcode();
                    $("#inputUsername").val("");
                    $("#inputPassword").val("");
                    $("#loginError").show();
                    $("#findPass").show();
                }
            })
    }
    @endif
    $(document).ready(function() {
        @if(!Session::has('user_id'))
        refresh_vcode();

        $("#lvcodeEmptyError").hide();
        $("#lvcodeError").hide();
        $("#rvcodeEmptyError").hide();
        $("#rvcodeError").hide();

        $("#loginvcodeImg").click(function(){
            var randId = new Date().getTime();
            $("#loginvcodeImg").attr("src", "{{ URL('vcode') }}/"+randId);
        });
        $("#regnewVcode").click(function(){
            var randId = new Date().getTime();
            $("#regvcodeImg").attr("src", "{{ URL('vcode') }}/"+randId);
        });
        $("#findPass").hide();
        $("#loginError").hide();
        $("#findPassEmptyError").hide();
        $("#findPassSuccess").hide();
        $("#findPassError").hide();
        $("#ul2").hide();
        $("#nameEmptyError").hide();
        $("#passwordEmptyError").hide();
        $("#nameRegError").hide();
        $("#emailEmptyError").hide();
        $("#emailRegError").hide();
        $("#nameError").hide();
        $("#passwordError").hide();
        $("#emailError").hide();

        $("#findPass").click(function(){
            $("#findPassEmptyError").hide();
            $("#findPassSuccess").hide();
            $("#findPassError").hide();
            var username = $("#inputUsername").val();
            if(username.length == 0) {
                $("#findPassEmptyError").show();
            } else {
                $.post("/login/findPass", {username: username,_token:"{{ csrf_token() }}"}, function (data) {
                    var arr = eval("(" + data + ")");
                    if(arr['status'] == true) {
                        $("#findPassSuccess").show();
                    } else {
                        $("#findPassError").show();
                    }
                })
            }
        })
        $("#inputUsername").keydown(function(event){
            if(event.which == 13)
                login();
        })
        $("#inputPassword").keydown(function(event){
            if(event.which == 13)
                login();
        })
        $("#loginvcodeText").keydown(function (event) {
            if(event.which == 13)
                login();
        })
        $("#sign").click(function(){
            login();
        });

        /*
         订正在状态页面登录出现的刷新问题
         */
        $("#signButton").click(function(){
            if (typeof(t) != "undefined") {
                window.clearInterval(t);
            }
        })
        $("#regButton").click(function(){
            if (typeof(t) != "undefined") {
                window.clearInterval(t);
            }
        })

        $("#reg").click(function() {
            $("#nameRegError").hide();
            $("#nameEmptyError").hide();
            $("#passwordEmptyError").hide();
            $("#emailEmptyError").hide();
            $("#nameError").hide();
            $("#passwordError").hide();
            $("#emailError").hide();
            $("#emailRegError").hide();

            //var vcode = $("#regvcodeText").val();
            var username = $("#Username").val();
            var nickname = $("#Nickname").val();
            var password = $("#Password").val();
            var password2 = $("#Password2").val();
            var email = $("#Email").val();
            if (username.length == 0)
                $("#nameEmptyError").show();
            else if(!(/^[a-zA-Z0-9_-]+$/.test(username)))
                $("#nameRegError").show();
            else if (password.length == 0)
                $("#passwordEmptyError").show();
            else if (password !== password2)
                $("#passwordError").show();
            else if (email.length == 0)
                $("#emailEmptyError").show();
            else  if(!(/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/.test(email)))
                $("#emailRegError").show();
            else
                $.post("{{ URL('user/register') }}", {
                    newUsername : username,
                    newPassword : password,
                    newPassword2 : password,
                    newNickname : nickname,
                    email : email,
                    _token:"{{ csrf_token() }}"
                }, function(data) {
                    if (data.status == 'username error') {
                        $("#nameError").show();
                    } else if (data.status == 'email error') {
                        $("#emailError").show();
                    } else if (data.status == true) {
                        window.location.reload();
                    }
                })
        })
        @endif
        $("#logout").click(function() {
            $.post("{{ URL("/user/logout") }}", {_token:"{{ csrf_token() }}"}, function(data) {
                var arr = eval(data);
                if (arr['status']) {
                    var d = new Date();
                    d.setTime(d.getTime() + (-1 * 24 * 60 * 60 * 1000));
                    var expires = "expires=" + d.toUTCString();
                    document.cookie = "username= ;" + expires;
                    window.location.reload();
                }
            })
        })
    })

</script>