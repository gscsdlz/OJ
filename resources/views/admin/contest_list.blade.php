@extends('admin.layout')
@section('title')
    比赛列表
@endsection
@section('main')
    <hr/>
    <div class="row">
        <div class="col-md-2">
            <ol class="text-danger">
                <li>一般情况下，请不要删除正在进行或已经完成的比赛</li>
                <li>当前界面仅仅能显示列表和删除，具体的编辑操作，点击编辑按钮进行</li>
            </ol>
        </div>
        <div class="col-md-8">
            <table class="table table-bordered">
                <tr>
                    <th>比赛编号</th>
                    <th>比赛名称</th>
                    <th>开始时间</th>
                    <th>结束时间</th>
                    <th>比赛权限</th>
                    <th>比赛类型</th>
                    <th>操作</th>
                </tr>
                @foreach($lists as $c)
                    <tr>
                        <td>{{ $c->contest_id }}</td>
                        <td>{{ $c->contest_name }}</td>
                        <td>{{ date('Y-m-d H:i:s', $c->c_stime) }}</td>
                        <td>{{ date('Y-m-d H:i:s', $c->c_etime) }}</td>
                        <td>@if($c->contest_pass == 1)公开比赛 @else 私有比赛 @endif</td>
                        <td>@if($c->oi == 0)ACM赛制 @else OI赛制 @endif</td>
                        <td>
                            <img style="cursor: pointer" width="15px" src="{{ URL('image/trash.png') }}" onclick="do_delete($(this))"/> |
                            <img style="cursor: pointer" width="15px" src="{{ URL('image/edit.png') }}" onclick="window.location.href='{{ URL('admin/contest/edit') }}' + '/' + {{ $c->contest_id }}"/>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="signModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <h2 class="modal-title text-danger">删除</h2>
                </div>
                <div class="modal-body">
                    <p class="text-danger">当前操作将会删除"<span id="cname" class="text-primary"></span>"的相关数据。请根据实际情况选择合适的选项！请注意，操作不可逆！</p>
                    <form class="form-horizontal" role="form">
                        <label><input type="checkbox" name="options" value="0" checked> 删除比赛</label><br/>
                        <label><input type="checkbox" name="options" value="1"> 删除比赛提交数据</label><br/>
                        <label><input type="checkbox" name="options" value="2"> 删除比赛参与小组信息</label><br/>
                        <label><input type="checkbox" name="options" value="3"> 删除比赛相关题目信息</label><br/>
                        <label><input type="checkbox" name="options" value="4"> 删除比赛问答信息</label>
                    </form>
                    <hr/>
                    <p class="text-danger" id="info"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" id="del">确认删除</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var cid;

        $(document).ready(function(){
            $("#del").click(function(){
                var options = new Array();
                $("input[type='checkbox']").each(function () {
                    if($(this).prop('checked') == true) {
                        options.push($(this).val())
                    }
                });
                if(options.length != 0) {
                    $.post("{{ URL('admin/contest/del') }}", {cid:cid, options:options, _token:"{{ csrf_token() }}"}, function(data){
                        window.location.reload();
                    })
                } else {
                    $("#info").html("还未选择任何删除选项！");
                }
            })
        })

        function do_delete(target) {
            cid = $(target).parent().parent().children().eq(0).html();
            $("#cname").html($(target).parent().parent().children().eq(1).html())
            $("#info").html("");
            $("#delModal").modal();
        }
    </script>
@endsection