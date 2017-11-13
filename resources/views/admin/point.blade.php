@extends('admin.layout')
@section('title')
    知识点编辑
@endsection
@section('main')
    <hr/>
    <div class="row">
        <div class="col-md-4">
            <ol style="color: red">
                <li></li>
            </ol>
        </div>
        <div class="col-md-4 well">
            <div class="panel panel-default">

                <div class="panel-body">
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2">知识点名</label>
                            <div class="col-sm-6">
                                <input type="text" value="" placeholder="请输入知识点名称" class="form-control" />
                            </div>
                            <button class="btn btn-danger col-sm-2" type="button" id="add">新增</button>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table table-striped table-bordered">
                <tr>
                    <th>编号</th>
                    <th>知识点名称</th>
                    <th>绑定题目数</th>
                    <th>操作</th>
                </tr>
                @foreach($lists as $point)
                    <tr>
                        <td>{{ $point->point_id }}</td>
                        <td>{{ $point->point_name }}</td>
                        <td>{{ $point->pros }}</td>
                        <td>
                            <img width="20px" onclick="change_form($(this))" src="{{ URL('image/edit.png') }}"/> |
                            <img width="20px" onclick="del($(this))" src="{{ URL('image/trash.png') }}"/>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
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
                    <button class="btn btn-primary btn-sm" type="button" data-dismiss="modal" id="confirm">确认</button>
                </div>
            </div>
        </div>
    </div>
    <script>

        var token = "{{ csrf_token() }}"
        var pid;
        var action;
        $(document).ready(function(){
            $("#confirm").click(function(){
                if(action == 'del') {
                    $.post("{{ URL('admin/point/del') }}", {pid:pid, _token:token}, function(data){
                        window.location.reload();
                    })
                }
            })

            $("#add").click(function(){
                var name = $(this).prev().children().eq(0).val();
                if(name.length != 0) {
                    $.post("{{ URL('admin/point/do_add') }}", {name: name, _token: token}, function (data) {
                        if (data.status == true) {
                            window.location.reload()
                        } else {
                            $("#info").html("更新失败，知识点重复！");
                            $("#alert").modal()
                        }
                    })
                } else {
                    $("#info").html("内容不能为空！");
                    $("#alert").modal()
                }
            })
        })

        function change_form(target) {
            if($(target).attr('src').indexOf('ac') == -1) {
                $(target).parent().prev().prev().html(
                    '<input type="text" value="' + $(target).parent().prev().prev().html() +
                    '" class="form-control">')
                $(target).attr('src', "{{ URL('image/ac.png') }}")
                $(target).next().attr('src', '{{ URL("image/wa.png") }}')

            } else {
                var name = $(target).parent().prev().prev().children().eq(0).val();
                var pid = $(target).parent().prev().prev().prev().html();
                if(name.length != 0) {
                    $.post("{{ URL('admin/point/do_edit') }}", {name: name, pid: pid, _token: token}, function (data) {
                        if (data.status == true) {
                            window.location.reload()
                        } else {
                            $("#info").html("更新失败，知识点重复！");
                            $("#alert").modal()
                        }
                    })
                } else {
                    $("#info").html("内容不能为空！");
                    $("#alert").modal()
                }
            }
        }

        function del(target) {
            if($(target).attr('src').indexOf('wa') == -1) {
                pid = $(target).parent().prev().prev().prev().html();
                action = 'del';
                $("#info").html("删除后，不可复原！");
                $("#alert").modal();
            } else {
                $(target).parent().prev().prev().html($(target).parent().prev().prev().children().eq(0).val());
                $(target).prev().attr('src', "{{ URL('image/edit.png') }}");
                $(target).attr('src', "{{ URL('image/trash.png') }}");
            }
        }
    </script>
@endsection