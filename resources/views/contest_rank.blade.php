@extends('layout')
@section('title')
    排名
@endsection
@section('main')
    <div class="row">
        <div class="col-md-10 col-md-offset-1 text-center">
            @if(($contest->options == 1 || $contest->options == 3) && !(Session::has('user_id') && Session::get('privilege') == 1))
            <div class="well"><h1 class="text-danger">本场比赛已经隐藏排名</h1></div>
            @else
            <h1>{{ $contest->contest_name }}</h1><p>&nbsp;</p>
            <form class="form-inline" role="form">
                <div class="form-group">
                    <label>显示小组排名</label>
                    <select class="form-control" id="groupfilter">
                        <option value="-1">ALL</option>
                        @if(isset($teams) && count($teams) != 0)
                            @foreach($teams as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <button id="filter" type="button" class="btn btn-primary">筛选</button>
                </div>
                <!--<div class="form-group">
                    <button id="export" type="button" class="btn btn-success " onclick="window.location.href='/Src/File/contest_rankList{#$contest#}.csv'">导出CSV数据</button>
                </div>-->
                @if(!is_null($ttl))
                    <br /><label class="text-danger">数据来自缓存，每5分钟更新一次, 请勿频繁刷新 下次更新还有{{  $ttl }}s</label>
                @endif

            </form>
            <nav aria-label="Page navigation">
                    <ul class="pagination pagination-lg">
                        <li>
                            <a href="{{ URL('/contest/rank/'.$cid.'/0/'.$team) }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        @for($i = 0 ; $i <= $pageT -1; $i++)
                            <li><a href="{{ URL('/contest/rank/'.$cid.'/'.$i.'/'.$team) }}">{{ $i }}</a></li>
                        @endfor
                        <li>
                            <a href="{{ URL('/contest/rank/'.$cid.'/'.($pageT - 1).'/'.$team) }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>

            <table class="table table-hover table-bordered"
                   style="vertical-align: middle; margin-top:20px;" id="ranks">
                <tr>
                    <th>排名</th>
                    <th>用户名</th>
                    <th>所在小组</th>
                    @if($contest->oi == 0)
                    <th>通过题目总数</th>
                    <th>总时长</th>
                    @else
                    <th>提交次数</th>
                    <th>分数</th>
                    @endif

                    @if(isset($ids) && count ($ids))
                    @foreach($ids as $row)
                        <th><a href="{{ URL('/contest/show/'.$cid.'/'.$row) }}">{{ $row }}</a></th>
                    @endforeach
                    @endif
                </tr>
                @if($contest->oi == 1)
                    @if(isset ($ranks) && count($ranks) > 0)
                        @foreach($ranks as $row)
                            {{--*/ $k = $row[5] /*--}}
                            <tr>
                                <td><span style="
                                    @if($row[1] > 0 && $k <= $contest->fe + $contest->cu + $contest->ag + $contest->au)
                                    @if($k <= $contest->au)color:rgb(255,215,0);font-size:30px;
                                    @elseif($k <= $contest->ag + $contest->au)color:rgba(0,0,0,0.5);font-size:30px;
                                    @elseif($k <= $contest->cu + $contest->ag + $contest->au)color:saddlebrown;font-size:30px;
                                    @else color:black;font-size:30px;
                                    @endif
                                    @endif
                                            ">{{ $k }}</span>
                                </td>
                                <td><a href="{{ URL('/user/show/'. $row[2]) }}">{{ $row[2] }}({{ $row[4] }})</a></td>
                                <td>{{ $row[3] }}</td>
                                <td>{{ $row[1] }}</td>
                                <td>{{ $row[0] }}</td>
                                @foreach($ids as $i)
                                @if(isset($row[$i]))
                                @if($row[$i][2] == true)
                                <td class="bg-success">{{ $row[$i][0] }}<br/>({{ $row[$i][1] }})
                                    @else
                                <td class="bg-danger">{{ $row[$i][0] }}<br/>({{ $row[$i][1] }})
                                    @endif
                                </td>
                                @else
                                <td></td>
                                @endif
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                @else
        @if(isset ($ranks) && count($ranks) > 0)
            @foreach($ranks as $row)
                {{--*/ $k = $row[5] /*--}}
                <tr>
                    <td><span style="
                        @if($row[1] > 0 && $k <= $contest->fe + $contest->cu + $contest->ag + $contest->au)
                        @if($k <= $contest->au)color:rgb(255,215,0);font-size:30px;
                        @elseif($k <= $contest->ag + $contest->au)color:rgba(0,0,0,0.5);font-size:30px;
                        @elseif($k <= $contest->cu + $contest->ag + $contest->au)color:saddlebrown;font-size:30px;
                        @else color:black;font-size:30px;
                        @endif
                            @endif
                        ">{{ $k }}</span>
                    </td>
                    <td><a href="{{ URL('/user/show/'. $row[2]) }}">{{ $row[2] }}({{ $row[4] }})</a></td>
                    <td>{{ $row[3] }}</td>
                    <td>{{ $row[1] }}</td>
                    <td><span class="times">{{ $row[0] }}</span></td>
                    @foreach($ids as $i)
                    @if(isset($row[$i]) )
                    @if(isset($row[$i][2]) && $row[$i][2] == 1)
                    <td class="bg-primary"><span class="times">{{ $row[$i][0] }}</span>@if($row[$i][1] != 0)<br/>(-{{  $row[$i][1] }})@endif
                        @elseif($row[$i][0] && !$row[$i][1] )
                    <td class="bg-success"><span class="times">{{ $row[$i][0] }}
                        @elseif($row[$i][0] && $row[$i][1])
                    <td class="bg-success"><span class="times">{{ $row[$i][0] }}</span><br/>(-{{ $row[$i][1] }})
                        @else
                    <td class="bg-danger text-center"><br/>(-{{$row[$i][1] }})
                    @endif
                    </td>
                    @else
                    <td></td>
                    @endif
                    @endforeach
                </tr>
                @endforeach
                @endif
            @endif
            </table>
            <nav aria-label="Page navigation">
                <ul class="pagination pagination-lg">
                    <li>
                        <a href="{{ URL('/contest/rank/'.$cid.'/0/'.$team) }}" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    @for($i = 0 ; $i <= $pageT -1; $i++)
                        <li><a href="{{ URL('/contest/rank/'.$cid.'/'.$i.'/'.$team) }}">{{ $i }}</a></li>
                    @endfor
                    <li>
                        <a href="{{ URL('/contest/rank/'.$cid.'/'.($pageT - 1).'/'.$team) }}" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        @endif
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $("#filter").click(function(){
                var group = $("#groupfilter").val();
                window.location.href = "{{ URL('/contest/rank/'.$cid.'/'. $pageN .'/') }}"+ '/' + group;
            })

            $(".times").each(function(){
                $(this).html(format($(this).html()));
            })
        })

        function format(time) {
            if(time < 0)
                time = -time;
            var h = parseInt(time / 60 / 60);
            time -= h * 60 * 60;
            var m = parseInt(time / 60);
            time -= m * 60;
            return h + ":" + m + ":" + time;
        }
    </script>
@endsection