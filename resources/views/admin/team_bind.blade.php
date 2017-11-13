@extends('admin.layout')
@section('title')
@endsection
@section('main')
    <hr>
    <div class="row">
        <div class="col-md-2">
            <ol class="text-danger">
                <li>红色背景表示私有小组，只允许管理员进行分配，普通用户不能进出</li>
                <li>点击垃圾桶形状的图标，即可删除该小组，该组成员统一分配到None组</li>
                <li>点击笔形状的图标，即可修改小组的信息，包括小组权限和小组的名称</li>
                <li>显示某个小组的用户列表时，可以使用模糊查找功能，选中持续搜索，可以保留上次搜索的结果</li>
                <li>调整小组时，务必首先选择目的小组</li>
                <li>显示用户列表时，有全选和全不选按钮，方便操作</li>
                <li>如果需要新增小组，点击，并输入即可，使用导入用户功能同样可以创建新的小组，为避免创建类似小组，例如ACM-ICPC ACM_ICPC请导入用户时，一定要来这里查阅</li>
            </ol>
        </div>
        <div class="col-md-2">
            <button class="btn btn-success btn-block" type="button" onclick="$('#addForm').toggle()">新增小组</button>
            <form class="form-horizontal well" id="addForm">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label><input type="radio" value="0" checked name="pri"> 开放小组</label>
                        <label><input type="radio" value="1" name="pri"> 私有小组</label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-8">
                        <input type="text" value="" id="teamName" placeholder="请输入新的小组名" class="form-control">
                    </div>
                    <button class="btn btn-danger btn-sm" type="button" id="addTeam">确认</button>
                </div>
            </form>
            <ul class="list-group" id="teamLists">
                @foreach($teamLists as $team)
                    <li style="cursor: pointer" class="list-group-item @if($team->private == 1)list-group-item-danger @endif" onclick="$('.col-md-2 ul li').removeClass('active');$(this).addClass('active');get_user_list({{ $team->team_id }})">

                        <img  onclick="form_add($(this).parent(), '{{ $team->team_id }}')" style="float: right;margin:0 10px" src="{{ URL('image/edit.png') }}" width="15px"/>
                        <img onclick="alert_del({{ $team->team_id }})" style="float: right;margin:0 10px" src="{{ URL('image/trash.png') }}" width="15px"/>
                        <span class="badge">{{ $team->mount }}</span>
                        <span>{{ $team->team_name }}</span>
                    </li>

                @endforeach
            </ul>
        </div>
        <div class="col-md-8 well">
            <form class="form-horizontal" role="form">
                <div class="form-group">
                    <label class="col-md-2 control-label">搜索以选中</label>
                    <div class="col-md-4">
                        <input type="text" id="key" value="" class="form-control" />

                    </div>
                    <label><input id="keepSelect" type="checkbox">&nbsp;&nbsp;持续选中</label>&nbsp;&nbsp;
                    <img onclick="do_filter($('#key').val())" style="cursor:pointer;" src="{{ URL('image/search.png') }}" width="30px" />
                </div>
            </form>
            <table class="table table-bordered" id="tables">
            </table>
        </div>
    </div>
    <div class="row">
        <div class="well col-md-6 col-md-offset-5 navbar-fixed-bottom text-center">

            <div class="col-sm-4">
                <select class="form-control" id="team_select">
                    <option value="-1">请选择小组</option>
                    @foreach($teamLists as $team)
                        <option value="{{ $team->team_id }}">{{ $team->team_name }}</option>
                    @endforeach
                </select>
            </div>
            <button style="float: left;margin: 0 10px" class="btn btn-danger btn-lg" type="button" id="changeTeam">调整小组</button>

            <button id="selectAll" style="float: right;margin: 0 10px" class="btn btn-primary btn-lg" type="button">全选</button>
            <button id="unSelect" style="float: right;margin: 0 10px" class="btn btn-primary btn-lg" type="button">全不选</button>
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
                    <button class="btn btn-muted btn-sm" type="button" data-dismiss="modal">取消</button>
                    <button class="btn btn-primary btn-sm" type="button" id="confirm">确认</button>
                </div>
            </div>
        </div>
    </div>
<script>

    var token = "{{ csrf_token() }}";
    var colNum = -1;
    var rowNum = -1;
    var row = 4;
    var tid = -1;
    function get_user_list(tid)
    {
        $.post("{{ URL('admin/user/get_users_byTeam') }}", {_token:token, tid:tid}, function(data){
            var len = data['res'].length;
            $("#tables").html("");
            var str;
            for(var i = 0; i < len / row; ++i) {
                str += '<tr>';
                for(var j = 0; j < row; ++j) {
                    if(i * row + j < len)
                        str += '<td><label><input type="checkbox" value="'+data['res'][i * row  + j]['user_id']+'" />&nbsp;&nbsp;&nbsp;<span>'+data['res'][i * row + j]['username']+'--('+data['res'][i * row + j]['nickname']+')</span></label></td>';

                }
                str += '</tr>';
            }
            $("#tables").html(str);
        })
    }

    function do_filter(key) {
        $("table input[type='checkbox']").each(function(){
            if($(this).next().html().indexOf(key) != -1) {
                $(this).parent().addClass('text-danger');
                $(this).prop('checked', true);
            } else {
                if($("#keepSelect").prop('checked') == false) {
                    $(this).parent().removeClass('text-danger');
                    $(this).prop('checked', false);
                }
            }
        })
    }


    $(document).ready(function () {

        $("#addForm").hide();

        $("#selectAll").click(function () {
            $("input[type='checkbox']").prop('checked', true);
        })

        $("#unSelect").click(function () {
            $("input[type='checkbox']").prop('checked', false);
        })


        $("#addTeam").click(function(){
            var private  = $("input[name='pri']:checked").val();
            var tname = $("#teamName").val();
            $.post("{{ URL('admin/team/add_team') }}", {private:private, tname:tname, _token:token} ,function(data){
                if(data.status == true) {
                    window.location.reload()
                } else {
                    $("#info").html("小组名重复！");
                    $("#alert").modal();
                }
            })
        })

        $("#confirm").click(function () {
            if(tid != -1) {
                $.post("{{ URL('/admin/team/del_team') }}", {tid:tid, _token:token}, function (data) {
                    window.location.reload();
                })
            } else {
                $("#alert").modal('hide');
            }
        })

        $("#changeTeam").click(function(){

            if($("#team_select").val() == -1) {
                $("#info").html("请选择目的小组！");
                $("#alert").modal();
            } else {
                var ids = new Array();
                $("#tables input[type='checkbox']").each(function(){
                    if($(this).prop('checked'))
                        ids.push(($(this).val()))
                })
                var tid = $("#team_select").val();
                $.post("{{ URL('admin/team/change_user') }}", {tid:tid, users:ids, _token:token}, function (data) {
                    window.location.reload();
                })
            }
        })

        $("#teamLists").on('click', 'form  .btn-danger', function () {
            var nprivate  = $("input[name='newPrivate']:checked").val();
            var ntname = $("#newTeamName").val();
            $.post('{{ URL('admin/team/change_team') }}', {tid:tid, private:nprivate, tname:ntname, _token:token}, function(data){
                if(data.status == true) {
                    window.location.reload()
                } else {
                    $("#info").html("小组名重复！修改失败");
                    $("#alert").modal();
                }
            })
        })
    })

    function alert_del(id) {
        tid = id;
        $("#info").html("删除后的所有小组成员将进入None小组");
        $("#alert").modal();
    }

    function form_add(target, id) {
        tid = id;
        $("#teamLists").children().each(function () {
            if($(this).hasClass('form-horizontal'))
                $(this).remove();
        })

        var str =
            '<form class="form-horizontal well">'+
                '<div class="form-group">'+
                    '<div class="col-sm-12">';

                    if($(target).hasClass('list-group-item-danger')) {
                        str +=
                        '<label><input type="radio" value="0" name="newPrivate"> 开放小组</label>'+
                        '<label><input type="radio" value="1" checked name="newPrivate"> 私有小组</label>';
                    } else {
                        str +=
                        '<label><input type="radio" value="0" checked name="newPrivate"> 开放小组</label>'+
                        '<label><input type="radio" value="1" name="newPrivate"> 私有小组</label>';
                    }

        str +=
                    '</div>'+
                '</div>'+
                '<div class="form-group">'+
                    '<div class="col-sm-8">'+
                        '<input type="text" value="'+$(target).children().eq(3).html()+'" id="newTeamName" placeholder="请输入新的小组名" class="form-control">'+
                    '</div>'+
                '<button class="btn btn-danger btn-sm" type="button" id="changeTeam">确认</button>'+
                '</div>'+
            '</form>';
        $(target).after(str);
    }
</script>
@endsection