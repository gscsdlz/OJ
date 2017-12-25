<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed"
                    data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span> <span
                        class="icon-bar"></span> <span class="icon-bar"></span> <span
                        class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">比赛模式@if(isset($contest))-{{ $contest->contest_name }}@endif</a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                @if(Session::has('user_id'))
                    <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" id="realname">{{ Session::get('username') }}<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ URL("/user/show/".Session::get('username')) }}">用户界面</a></li>
                            @if(Session::get('privilege') == 1)
                                <li><a href="{{ URL('admin/') }}">后台管理</a></li>
                            @endif
                            <li class="divider"></li>
                            <li id="logout"><a href="#">退出登录</a></li>
                        </ul>
                    </li>
                @else
                    <li><a href="{{ URL('login') }}">登录 / 注册</a></li>
                @endif
            </ul>
            <ul class="nav navbar-nav  navbar-right">
                <li><a href="{{ URL('contest/list') }}">返回</a></li>
                <li @if(isset($menu) && $menu == 'contest@show') class="active" @endif><a href="{{ URL('contest/show/'.$cid) }}">题目</a></li>
                <li @if(isset($menu) && $menu == 'status') class="active" @endif><a href="{{ URL('status?cid='.$cid) }}">状态</a></li>
                <li @if(isset($menu) && $menu == 'contest@rank') class="active" @endif><a href="{{ URL('contest/rank/'.$cid) }}">排名</a></li>
                <li @if(isset($menu) && $menu == 'contest@ask') class="active" @endif><a href="{{ URL('contest/ask/'.$cid) }}">问答</a></li>
            </ul>


        </div>
    </div>
</nav>
<hr/>
<hr/>

<script type="text/javascript">
    @if(Session::has('user_id'))
    $(document).ready(function() {
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
    @endif
</script>
