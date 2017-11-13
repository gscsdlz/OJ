@extends('layout')
@section('title')提交状态@endsection
@section('main')
    <div class="row">
        <div class="col-md-8 col-md-offset-2  text-center">
            <form class="form-inline" role="form" action="" method="get">
                {{ csrf_field() }}
                <div class="form-group">
                    <input type="text" placeholder="题目编号" class="form-control" name="pid" value="@if(!is_null($pid)){{ $pid }}@endif" />
                </div>
                <div class="form-group">
                    <input type="text" placeholder="用户名" class="form-control" name="user" value="@if(!is_null($user)){{ $user }}@endif" />
                </div>
                <div class="form-group">
                    <label>语言</label>
                    <select class="form-control" name="lang">
                        {{--*/ $langArr = config('web.langArr') /*--}}
                        @for($i = 0; $i < count($langArr); $i++)
                            @if(!is_null($lang) && $lang == $i)
                                <option selected value="{{ $i }}">{{ $langArr[$i] }}</option>
                            @else
                                <option value="{{ $i }}">{{ $langArr[$i] }}</option>
                            @endif
                        @endfor
                    </select>
                </div>
                <div class="form-group">
                    <label>状态</label>
                    <select class="form-control" name="status">
                        {{--*/ $statusArr = config('web.statusArr') /*--}}
                        @for($i = 0; $i < count($statusArr); $i++)
                            @if(!is_null($status) && $status == $i)
                                <option selected value="{{ $i }}">{{ $statusArr[$i] }}</option>
                            @else
                                <option value="{{ $i }}">{{ $statusArr[$i] }}</option>
                            @endif
                        @endfor
                    </select>
                </div>
                <input type="hidden" value="@if(!is_null($cid)){{ $cid }}@endif" name="cid">
                <button type="submit" class="btn btn-default btn-primary">筛选</button>
            </form>
            <hr />
            <table class="table table-hover">
                <tr>
                    <th>提交号</th>
                    <th>提交时间</th>
                    <th>题目编号</th>
                    <th>运行时间(MS)</th>
                    <th>运行内存(KB)</th>
                    <th>代码长度(B)</th>
                    <th>语言</th>
                    <th>状态</th>
                    @if($contest != '' && $contest->oi == 1)
                    <th>分数</th>
                    @endif
                    <th>用户名</th>
                </tr>
                {{--*/ $Infos = config('web.statusInfo') /*--}}
               @foreach($lists as $row)
               <tr>
                   <td>{{ $row['submit_id'] }}</td>
                   <td>{{ date('Y-m-d H:i:s', $row['submit_time']) }}</td>
                   @if(isset($cid) && $cid != 0)
                       <td><a href="{{ URL('/contest/show/'.$cid.'/'.$row['pro_id']) }}">{{ $row['pro_id'] }}</a></td>
                   @else
                       <td><a href="{{ URL('/problem/show/'.$row['pro_id']) }}">{{ $row['pro_id'] }}</a></td>
                   @endif
                   <td>{{ $row['run_time'] }}</td>
                   <td>{{ $row['run_memory'] }}</td>
                   @if(Session::has('user_id') && (Session::get('user_id') == $row['user_id'] || Session::get('privilege') == 1))
                       <td><a href="{{ URL('/code/show/'.$row['submit_id']).'/'.$cid }}">{{ $row['code_length'] }}</a></td>
                   @else
                       <td>{{ $row['code_length'] }}</td>
                   @endif
                   <td>{{ $langArr[$row['lang']] }}</td>
                   <td>
                       @if($row['status'] == 4)
                           <span class="text-danger">
                       @elseif($row['status'] == 5)
                           <span class="text-warning">
                       @elseif($row['status'] == 11)
                           <span class="text-primary">
                       @elseif($row['status'] > 5 && $row['status'] < 11)
                           <span class="text-success">
                       @else
                           <span class="text-muted">
                       @endif
                       <span data-toggle="tooltip" title="{{ $Infos[$row['status']] }}">
                            @if($row['status'] == 11 && Session::has('user_id') && (Session::get('user_id') == $row['user_id'] || Session::get('privilege') == 1))
                                <a href="{{ URL("code/compiler_show/".$row['submit_id'].'/'.$cid ) }}">{{ $statusArr[$row['status']] }}</a>
                            @else
                                {{  $statusArr[$row['status']] }}
                            @endif
                       </span></span>
                   </td>

                   @if($contest != '' && $contest->oi == 1)
                       <td>{{ $row['score'] }}</td>
                   @endif
                   <td><a href="{{ URL('user/show/'.$row['username']) }}">{{ $row['username'] }}</a></td>
               </tr>
               @endforeach
            </table>
            <nav>
                <ul class="pagination pagination-lg text-center">
                    <li><a href="{{ URL('/status') }}?cid=@if(!is_null($cid)){{ $cid }}@endif&pid=@if(!is_null($pid)){{ $pid }}@endif&user=@if(!is_null($user)){{ $user }}@endif&lang=@if(!is_null($lang)){{ $lang }}@endif&status=@if(!is_null($status)){{ $status }}@endif">首页</a></li>
                    <li><a href="#">...</a></li>
                    <li><a href="{{ URL('/status') }}?ridr={{ $ridl + 1 }}&cid=@if(!is_null($cid)){{ $cid }}@endif&pid=@if(!is_null($pid)){{ $pid }}@endif&user=@if(!is_null($user)){{ $user }}@endif&lang=@if(!is_null($lang)){{ $lang }}@endif&status=@if(!is_null($status)){{ $status }}@endif">上一页</a></li>
                    <li><a href="#">...</a></li>
                    <li><a href="{{ URL('/status') }}?ridl={{ $ridr - 1 }}&cid=@if(!is_null($cid)){{ $cid }}@endif&pid=@if(!is_null($pid)){{ $pid }}@endif&user=@if(!is_null($user)){{ $user }}@endif&lang=@if(!is_null($lang)){{ $lang }}@endif&status=@if(!is_null($status)){{ $status }}@endif">下一页</a></li>
                    <li><a href="#">...</a></li>
                    <li><a href="{{ URL('/status') }}?ridr=1&cid=@if(!is_null($cid)){{ $cid }}@endif&pid=@if(!is_null($pid)){{ $pid }}@endif&user=@if(!is_null($user)){{ $user }}@endif&lang=@if(!is_null($lang)){{ $lang }}@endif&status=@if(!is_null($status)){{ $status }}@endif">尾页</a></li>
                </ul>
            </nav>
        </div>
    </div>
    <script>
        var t;
        $(document).ready(function(){
            t=setTimeout("location.href='" + "{{ URL('/status') }}?@if(!is_null($ridl))&ridl={{ $ridl }}@elseif(!is_null($ridr))&ridr={{ $ridr }}@endif&cid=@if(!is_null($cid)){{ $cid }}@endif&pid=@if(!is_null($pid)){{ $pid }}@endif&user=@if(!is_null($user)){{ $user }}@endif&lang=@if(!is_null($lang)){{ $lang }}@endif&status=@if(!is_null($status)){{ $status }}@endif';", 10000)
        })
        @if(Session::has('user_id') &&  Session::get('privilege') == 1)
        function rejudge(id) {
            $.post("{{ URL('/admin/contestM/rejudge') }}", {submit_id:id,cid:-1},function(data){
                ;
            })
        }
        @endif
        $(function () { $("[data-toggle='tooltip']").tooltip(); });
    </script>
@endsection