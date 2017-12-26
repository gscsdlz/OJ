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
                <li><a href="{{ URL('login') }}">登录 / 注册</a></li>
            @endif
            </ul>

        </div>
    </div>
</nav>
<hr/>
<hr/>
<script type="text/javascript">

    @if(Session::has('user_id'))
        $(document).ready(function(){
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