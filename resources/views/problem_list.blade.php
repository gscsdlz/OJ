@extends('layout')
@section('title')
    题目列表
@endsection
@section('main')
<div class="row">
    <div class="col-md-6 col-md-offset-3 text-center">
        <nav>
            <ul class="pagination pagination-lg">
                <li><a href="{{ URL('problem/page/0') }}">&laquo;</a></li>
                @for($i = 1; $i <= $mount; $i++)
                    <li @if($page == $i)class="disabled" @endif><a href="{{ URL('problem/page/'.$i) }}">{{ $i }}</a></li>
                @endfor
                <li><a href="{{ URL('problem/page/'.$mount) }}">&raquo;</a></li>
            </ul>
        </nav>
        <table class="table table-hover text-left">
            <tr>
                <th>状态</th>
                <th>题目编号</th>
                <th>题目名</th>
                <th>题目通过率</th>
                <th>知识点分类</th>
            </tr>
            @foreach($lists as $row)
                <tr>
                    <td>
                        @if($row->getACFlag() == 1)
                            <img src="{{ URL('image/ac.png') }}" width="20px">
                        @elseif($row->getACFlag() == -1)
                            <img src="{{ URL('image/wa.png') }}" width="20px">
                        @endif
                    </td>
                    <td>{{ $row->pro_id }}</td>
                    <td align="left"><a href="{{ URL('problem/show/'.$row->pro_id) }}">{{ $row->pro_title }}</a></td>
                    <td>
                        @if($row->getAllNum() == 0)
                            &nbsp;&nbsp;0%
                        @else
                            {{ (int)($row->getACNum() * 100 / $row->getAllNum()) }}%
                        @endif
                        ({{ $row->getACNum() }}/{{ $row->getAllNum() }})
                    </td>
                    <td>
                        @foreach($row->getPoints() as $point)
                            <span class="label label-default">{{ $point }}</span>
                        @endforeach
                    </td>
                </tr>
            @endforeach

        </table>
        <nav>
            <ul class="pagination pagination-lg">
                <li><a href="{{ URL('problem/page/0') }}">&laquo;</a></li>
                @for($i = 1; $i <= $mount; $i++)
                    <li @if($page == $i)class="disabled" @endif><a href="{{ URL('problem/page/'.$i) }}">{{ $i }}</a></li>
                @endfor
                <li><a href="{{ URL('problem/page/'.$mount) }}">&raquo;</a></li>
            </ul>
        </nav>
    </div>
</div>

@endsection

