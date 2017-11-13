@extends('layout')
@section('title')
    比赛列表
@endsection
@section('main')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span>@if(isset($old) && $old == true)全部比赛@else还未开始的比赛/正在进行的比赛@endif</span>
                    <span style="float: right;">@if(isset($old) && $old == false)<a href="{{ URL('contest/list/old') }}">显示全部比赛</a>@else<a href="{{ URL('contest/list') }}">显示未过期比赛</a>@endif</span>
                </div>
                <div class="panel-body">
                    <table class="table">
                        <tr>
                            <th>编号</th>
                            <th>比赛名称</th>
                            <th>比赛开始时间</th>
                            <th>比赛结束时间</th>
                            <th>比赛权限</th>
                            <th>比赛类型</th>
                        </tr>
                        @foreach($lists as $row)
                            <tr>
                                <td>{{ $row->contest_id }}</td>
                                <td><a href="{{ URL('contest/show/'.$row->contest_id) }}">{{ $row->contest_name }}</a></td>
                                <td><span data-toggle="tooltip" title="
                                @if($row->c_etime < time())
                                            比赛已过期
                                @elseif($row->c_etime - time() > 24 * 60 * 60 * 60)
                                            距离比赛开始：远超一天
                                @elseif($row->c_stime > time())
                                            距离比赛开始：{{ date('H:i:s', $row->c_etime - time()) }}
                                @else
                                        比赛已经开始
                                @endif
                                ">{{ date('Y-m-d H:i:s', $row->c_stime) }}</span></td>
                                <td>{{ date('Y-m-d H:i:s', $row->c_etime) }}</td>
                                <td>
                                    @if($row->contest_pass == 1)
                                        公开比赛
                                    @else
                                        私有比赛
                                    @endif
                                </td>
                                <td>
                                    @if($row->oi == 0)
                                        ACM赛制
                                    @else
                                        OI赛制
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function () { $("[data-toggle='tooltip']").tooltip(); });
    </script>
@endsection
