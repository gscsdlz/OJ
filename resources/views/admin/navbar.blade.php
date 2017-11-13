<ul class="nav nav-tabs">
    <li role="presentation" @if(isset($menu) && $menu == 'index')class="active" @endif><a href="#">首页</a></li>
    <li role="presentation" class="dropdown @if(isset($menu) && $menu == 'problem')active @endif">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">题目管理<span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li><a href="{{ URL('admin/problem/add') }}">新增题目</a></li>
            <li><a href="{{ URL('admin/problem/list/1') }}">修改/删除</a></li>
            <li><a href="{{ URL('admin/problem/show_visible') }}">查看隐藏题目</a></li>
            <li><a href="{{ URL('admin/point/edit') }}">知识点编辑</a></li>
        </ul>
    </li>
    <li role="presentation" class="dropdown @if(isset($menu) && $menu == 'contest')active @endif">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">比赛管理<span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li><a href="{{ URL('admin/contest/edit') }}">新增比赛</a></li>
            <li><a href="{{ URL('admin/contest/list') }}">修改/删除</a></li>
            <li><a href="{{ URL('admin/balloon') }}">气球分发</a></li>
            <li><a href="#">代码查重</a></li>
            <li><a href="#">比赛数据导出</a></li>
        </ul>
    </li>
    <li role="presentation" class="dropdown @if(isset($menu) && $menu == 'user')active @endif">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">用户管理<span class="caret"></span></a>
        <ul class="dropdown-menu">
            <li><a href="{{ URL('admin/team/show') }}">小组修改</a></li>
            <li><a href="{{ URL('admin/user/import') }}">用户导入</a></li>
        </ul>
    </li>
    <li role="presentation"><a href="#">帮助文件</a></li>
    <li role="presentation"><a href="{{ URL('/index') }}">返回前台</a></li>
</ul>