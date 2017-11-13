@extends('layout')
@section('title')
    用户排名
@endsection
@section('main')
<div class="row">
    <div class="col-md-8 col-md-offset-2 text-center">
        <table class="table table-hover text-left">
            <tr>
                <th>排名</th>
                <th>用户名</th>
                <th>签名</th>
                <th>通过题目数</th>
                <th>提交题目数</th>
                <th>AC率</th>
            </tr>
            @foreach($users as $row)
            <tr>
                <td><span style="font-size: 18px;color:orange">{{ $row[6] }}</span></td>
                <td><a href="{{ URL("/user/show/".$row[1]) }}">{{ $row[1] }}({{ $row[2] }})</a></td>
                <td><span class="label label-primary">{{ $row[3] }}</span></td>
                <td>{{ $row[4] }}</td>
                <td>{{ $row[5] }}</td>
                    @if($row[5] != 0)
                        <td>{{ number_format($row[4] / $row[5] * 100, 2, '.', '') }}%</td>
                    @else
                        <td>{{ number_format($row[4] / 1 * 100, 2, '.', '') }}%</td>
                    @endif
            </tr>
            @endforeach
        </table>
        <nav>
            <ul class="pagination pagination-lg text-center">
                <li><a href="{{ URL("/rank/show/1") }}">首页</a></li>
                <li><a href="#">...</a></li>
                <li><a href="{{ URL("/rank/show/".($page - 1)) }}">上一页</a></li>
                <li><a href="#">...</a></li>
                <li><a href="{{ URL("/rank/show/".($page + 1)) }}">下一页</a></li>
                <li><a href="#">...</a></li>
                <li><a href="{{ URL("/rank/show/".$maxpage) }}">尾页</a></li>
            </ul>
        </nav>
    </div>
</div>
@endsection
