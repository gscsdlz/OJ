@extends('admin.layout')
@section('title')
    @if(isset($problem)){{ $problem->pro_title }}@else新增题目@endif
@endsection
@section('main')
    <hr/>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label class="control-label col-sm-2">题目名</label>
                    <div class="col-sm-10">
                        <input type="text"  value="@if(isset($problem)){{ $problem->pro_title }}@endif" class="form-control" id="pro_title"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">时间限制</label>
                    <div class="col-sm-4">
                        <input type="number"  @if(isset($problem))value="{{ $problem->time_limit }}"@else value="1000"@endif class="form-control" id="time_limit"/>
                    </div>
                    <label class="control-label col-sm-2">内存限制</label>
                    <div class="col-sm-4">
                        <input type="number"  @if(isset($problem))value="{{ $problem->memory_limit }}"@else value="65535"@endif class="form-control" id="mem_limit"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">来源</label>
                    <div class="col-sm-10">
                        <input type="text"  @if(isset($problem))value="{{ $problem->author }}"@else value="{{ Session::get('username') }}"@endif class="form-control" id="author"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2">知识点</label>
                    <div class="col-sm-10"  id="points">
                        @foreach($pointLists as $row)
                            {{--*/ $falg = false /*--}}
                        <label class="checkbox-inline">
                            @foreach($points as $p)
                                @if($p->point_id == $row->point_id)
                                    {{--*/ $flag = true /*--}}
                                    <input type="checkbox" checked value="{{ $row->point_id }}"> {{ $row->point_name }}
                                @break
                                @endif
                            @endforeach
                            @if(!isset($flag) || !$flag)
                                <input type="checkbox" value="{{ $row->point_id }}"> {{ $row->point_name }}
                            @endif
                        </label>
                        @endforeach

                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="text-left">题目描述<img src="{{ URL('image/close.png') }}" onclick="$(this).parent().parent().next().toggle()" style="float: right;cursor: pointer" width="20px"></h3>
                </div>
                <div class="panel-body">
                    <script id="editor" type="text/plain"></script>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="text-left">输入描述<img src="{{ URL('image/close.png') }}" onclick="$(this).parent().parent().next().toggle()" style="float: right;cursor: pointer" width="20px"></h3>
                </div>
                <div class="panel-body">
                    <script id="editorIn" type="text/plain"></script>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="text-left">输出描述<img src="{{ URL('image/close.png') }}" onclick="$(this).parent().parent().next().toggle()" style="float: right;cursor: pointer" width="20px"></h3>
                </div>
                <div class="panel-body">
                    <script id="editorOut" type="text/plain"></script>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="text-left">提示<img src="{{ URL('image/close.png') }}" onclick="$(this).parent().parent().next().toggle()" style="float: right;cursor: pointer" width="20px"></h3>
                </div>
                <div class="panel-body">
                    <script id="editorHint" type="text/plain"></script>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5 col-md-offset-1">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="text-left">输入样例</h3>
                </div>
                <div class="panel-body">
                    <textarea class="form-control" rows="10" id="s_in">@if(isset($problem)){{ $problem->pro_dataIn }}@endif</textarea>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="text-left">输出样例</h3>
                </div>
                <div class="panel-body">
                    <textarea class="form-control" rows="10" id="s_out">@if(isset($problem)){{ $problem->pro_dataOut }}@endif</textarea>
                </div>
            </div>
        </div>
    </div>
    <hr/>
    @if(isset($pid) && $pid > 0)
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-success">
                <div class="panel-heading">
                    输入输出文件列表
                </div>
                <div class="panel-body">
                    <form id="uploadImg" class="form-horizontal" role="form" method="post" action="" enctype="multipart/form-data">
                        <label>请选择.zip或者.in .out文件：<input class="form-control" type="file" name="file" id="uploadFile" /></label>
                    </form>
                    <hr/>
                    <table class="table table-bordered table-stripe">
                        <tr>
                            <th>编号</th>
                            <th>文件名</th>
                            <th>上传时间</th>
                            <th>文件大小</th>
                            <th>操作</th>
                        </tr>
                        @for($i = 1; $i <= count($fileLists); $i++)
                            <tr>
                                <td>{{ $i }}</td>
                                <td>{{ $fileLists[$i - 1]['filename'] }}</td>
                                <td>{{ $fileLists[$i - 1]['mtime'] }}</td>
                                <td>{{ $fileLists[$i - 1]['size'] }}</td>
                                <td><a href="{{ URL('admin/file/download/'.$pid.'/'.$fileLists[$i - 1]['filename']) }}" target="_blank">下载</a> |
                                    <a href="javascript:;" onclick="do_del({{ $pid }}, $(this))">删除</a></td>
                            </tr>
                        @endfor
                    </table>
                    <a href="{{ URL('admin/file/downloads/'.$pid) }}" target="_blank" style="float: right;" class="btn btn-primary" type="button">批量下载</a>
                </div>
            </div>
        </div>
    </div>
    @endif
    <button class="btn btn-success btn-block navbar-fixed-bottom" id="submit" type="button">保存题目</button>

    <div class="modal" id="alert" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm" role="document">

            <div class="modal-content">
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <p>提示</p>
                    </div>
                </div>
                <div class="panel-body">
                    <p id="info"></p>
                </div>
                <div class="panel-footer text-right">
                    <button class="btn btn-primary btn-sm" type="button" data-dismiss="modal" id="confirm">关闭</button>
                </div>
            </div>
        </div>
    </div>
<script src="{{ URL('/ext/ueditor/ueditor.config.js') }}"></script>
<script src="{{ URL('/ext/ueditor/ueditor.all.min.js') }}"> </script>
<script src="{{ URL('/ext/ueditor/lang/zh-cn/zh-cn.js') }}"></script>
<script>
    var ue = UE.getEditor('editor');
    var ueIn = UE.getEditor('editorIn');
    var ueOut = UE.getEditor('editorOut');
    var ueHint = UE.getEditor('editorHint');
    var arr;
    
    $(document).ready(function () {
        $("#editor").css('width', '100%');
        $("#editor").css('height', '400px');

        $("#editorIn").css('width', '100%');
        $("#editorIn").css('height', '400px');

        $("#editorOut").css('width', '100%');
        $("#editorOut").css('height', '400px');

        $("#editorHint").css('width', '100%');
        $("#editorHint").css('height', '400px');

        @if(isset($pid) && $pid > 0)
        $.post("{{ URL('admin/problem/get_others') }}", {pid:{{ $pid }}, _token:"{{ csrf_token() }}"}, function(data){
            ueIn.ready(function () {
                ueIn.setContent(data[0].pro_in);
            })

            ueOut.ready(function () {
                ueOut.setContent(data[0].pro_out);
            })

            ueHint.ready(function () {
                ueHint.setContent(data[0].hint);
            })

            ue.ready(function () {
                ue.setContent(data[0].pro_descrip)
            })
        })

        $("#uploadFile").AjaxFileUpload({
            action: "{{ URL('admin/file/upload/'.$pid.'?_token='.csrf_token()) }}",
            onComplete: function(filename, response) {
                var str = response.replace(/<pre>/, '');
                str = str.replace(/<\/pre>/, '');
                response = eval("("+str+")");
                if(response.status == true)
                    window.location.reload();
                else
                    alert("上传失败，请重试");
            }
        })
        @endif
        $("#submit").click(function(){
            var pro_title = $("#pro_title").val();
            var timel = $("#time_limit").val();
            var meml = $("#mem_limit").val();
            var author = $("#author").val();
            var pro_descrip = ue.getContent();
            var pro_in = ueIn.getContent();
            var pro_out = ueOut.getContent();
            var pro_hint = ueHint.getContent();
            var pro_sin = $("#s_in").val();
            var pro_sout = $("#s_out").val();
            var points = new Array();
            $("#points").children().each(function () {
                if($(this).children().eq(0).prop('checked') == true) {
                    points.push($(this).children().eq(0).val())
                }
            })
            $.post("{{ URL('admin/problem/insert') }}", {
                @if(isset($pid) && $pid > 0)
                pid:{{ $pid }},
                @endif
                pro_title:pro_title,
                timel:timel,
                meml:meml,
                author:author,
                pro_descrip:pro_descrip,
                pro_in:pro_in,
                pro_out:pro_out,
                pro_hint:pro_hint,
                pro_sin:pro_sin,
                pro_sout:pro_sout,
                points:points,
                _token:"{{ csrf_token() }}"
            },function (data){
                if(data.status == true) {
                    alert("保存成功！");
                    window.location.href = '{{ URL('admin/problem/add/') }}'+ '/' +data.info;
                } else {
                    if(data.info == 'empty') {
                        $("#info").html("提交包含空信息，标题，时间限制，内存限制，输入描述，输出描述，题目描述为必填项！");
                        $("#alert").modal();
                    } else {
                        $("#info").html("系统错误，请重试！");
                        $("#alert").modal();
                    }
                }
            })
        })
    })
    @if(isset($pid) && $pid > 0)
    function do_del(pid, target) {
        var name = $(target).parent().parent().children().eq(1).html();
        $.post("{{ URL('admin/file/del') }}", {pid:pid, name:name, _token:"{{ csrf_token() }}"}, function(data){
            if(data.status == true) {
                var id = $(target).parent().parent().children().eq(0).html();
                $(target).parent().parent().html(
                    '<td colspan="4" class="text-center text-danger">文件已软删除，移动至<span>'+ data.path+'</span>' +
                    '<td><a href="javascript:;" onclick="redo($(this), '+id+')">恢复</a></td>'
                );
            } else {
                alert("删除失败!");
                window.location.reload();
            }
        })

    }

    function redo(target, id) {
        var path = $(target).parent().prev().children().eq(0).html();
        $.post("{{ URL('admin/file/undo') }}", {name:path, _token:"{{ csrf_token() }}"}, function(data){
            if(data.status == true){
                $(target).parent().parent().html(
                    '<td>'+id+'</td>'+
                    '<td>'+data['res'][0]+'</td>'+
                    '<td>'+data['res'][2]+'</td>'+
                    '<td>'+data['res'][1]+'</td>'+
                    '<td><a href="{{ URL('admin/file/download/'.$pid) }}/'+data['res'][0]+'" target="_blank">下载</a> |' +
                    '<a href="javascript:;" onclick="do_del({{ $pid }}, $(this))">删除</a></td>'
                )
            } else {
                alert("文件恢复失败，请重新上传");
                window.location.reload();
            }
        })
    }
    @endif
</script>
@endsection