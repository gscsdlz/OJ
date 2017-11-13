@extends('admin.layout')
@section('title')
    题目管理-第{{ $page }}页
@endsection
@section('main')
    <hr/>
    <div class="row">
        <div class="col-md-3">
            <ul class="text-danger">
                <li>1.点击第一个隐藏图标，即可隐藏题目，管理员可以查看</li>
                <li>2.点击第二个笔形图标，即可编辑题目</li>
                <li>3.点击第三个垃圾桶，即可删除题目</li>
            </ul>
        </div>
        <div class="col-md-6 text-center">
            <nav>
                <ul class="pagination pagination-lg">
                    <li><a href="{{ URL('admin/problem/list/0') }}">&laquo;</a></li>
                    @for($i = 1; $i <= $mount; $i++)
                        <li @if($page == $i)class="disabled" @endif><a href="{{ URL('admin/problem/list/'.$i) }}">{{ $i }}</a></li>
                    @endfor
                    <li><a href="{{ URL('admin/problem/list/'.$mount) }}">&raquo;</a></li>
                </ul>
            </nav>
            <hr/>
            <table class="table table-hover table-bordered text-left">
                <tr>
                    <th>题目编号</th>
                    <th>题目名称</th>
                    <th>可见性</th>
                    <th>操作</th>
                </tr>
                @foreach($proLists as $pro)
                    <tr onmousemove='$("td img").hide();$(this).children().eq(3).children().show()'>
                        <td><a href="{{ URL('problem/show/'.$pro->pro_id) }}">{{ $pro->pro_id }}</a></td>
                        <td>{{ $pro->pro_title }}</td>
                        <td><input type="hidden" value="{{ $pro->visible }}" />@if($pro->visible == 0) <span class="label label-danger">不可见</span> @else 可见 @endif</td>
                        <td>
                            <img class="visible" src="{{ URL('image/hide.png') }}" width="20px" style="cursor: pointer"/>&nbsp;&nbsp;&nbsp;&nbsp;
                            <img onclick="window.location.href='{{ URL('admin/problem/add/'.$pro->pro_id) }}'" src="{{ URL('image/edit.png') }}" width="20px" style="cursor:pointer;"/>&nbsp;&nbsp;&nbsp;&nbsp;
                            <img class="trash" src="{{ URL('image/trash.png') }}" width="20px" style="cursor: pointer"/>&nbsp;&nbsp;&nbsp;&nbsp;
                        </td>
                    </tr>
                @endforeach
            </table>
            <hr/>
            <nav>
                <ul class="pagination pagination-lg">
                    <li><a href="{{ URL('admin/problem/list/0') }}">&laquo;</a></li>
                    @for($i = 1; $i <= $mount; $i++)
                        <li @if($page == $i)class="disabled" @endif><a href="{{ URL('admin/problem/list/'.$i) }}">{{ $i }}</a></li>
                    @endfor
                    <li><a href="{{ URL('admin/problem/list/'.$mount) }}">&raquo;</a></li>
                </ul>
            </nav>
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
                    <button class="btn btn-primary btn-sm" type="button" id="confirm">确认</button>
                </div>
            </div>
        </div>
    </div>
<script>

    var confirm_menu = 0;
    var pid;
    var token = "{{  csrf_token() }}";
    $(document).ready(function () {

        $("td img").hide();

        $(".visible").click(function(){
            var id = $(this).parent().parent().children().eq(0).html();

            var visible = $(this).parent().prev().children().eq(0).val() == 0 ? 1 : 0;

            $.post("{{ URL("admin/problem/do_visible") }}", {
                _token:token,
                pid:id,
                visible:visible,
            }, function(data){
                if(data.status == true) {
                    $("#info").html("设置成功，即将自动跳转！");
                    $("#alert").modal();
                } else {
                    $("#info").html("设置失败，请重试！");
                    $("#alert").modal();
                }
            })
            setInterval("window.location.reload()", 3000);
        })

        $(".trash").click(function () {
            pid = $(this).parent().parent().children().eq(0).html();

            $("#info").html('<span class="text-danger">删除不可逆，谨慎操作！</span>');
            $("#alert").modal();

            confirm_menu = 1;
        })


        $("#confirm").click(function(){
            if(confirm_menu == 1) {
                $.post("{{ URL('admin/problem/del_pro') }}", {
                    _token:token,
                    pid:pid,
                }, function(data){
                    if(data.status == true) {
                        $("#info").html("删除成功，即将自动跳转！");
                        $("#alert").modal();
                    } else {
                        $("#info").html("删除失败，请重试！");
                        $("#alert").modal();
                    }
                })
                setInterval("window.location.reload()", 3000);
            }
        })

    })
</script>
@endsection