@extends('admin.layout')
@section('title')
    导入用户
@endsection
@section('main')
    <hr>
    <div class="row">
        <div class="col-md-3">
            <ul class="text-danger">
                <li>上传的文件只能是CSV文件，最好使用UTF-8（无BOM）编码</li>
                <li>上传的字段包含五个、账号、昵称、密码、小组名、座位号</li>
                <li>其中密码使用明文密码，例如123456</li>
                <li>小组名最好提前请从用户管理——小组修改中获取，如果填写了不存在的小组，将会建立新的小组名</li>
                <li>座位号可以为空字符串，但是不能不写</li>
                <li>字段之间推荐使用,(英文)隔开，每个字段可以使用双引号</li>
                <li>标准样例：</li>
                <li><pre>"admin","root","123456","23","666_6"</pre></li>
                <li>上传文件的第一行，请不要包含标题栏</li>
                <li>修改字段分隔符以后，请点击空白页面，等待页面自刷新，来更新参数</li>
                <li>双击每一个单元格可以修改，修改完成以后敲击回车即可</li>
                <li>点击右边的垃圾桶图标可以删除所有的这一行数据</li>
                <li>请注意，上传的文件会堆积起来，也就是上传一个文件检查完，就应该保存</li>
            </ul>
        </div>
        <div class="col-md-6">
            <form id="uploadCSV" class="form-horizontal" role="form" method="post" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="control-label col-sm-2">字段分隔符</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="delimiter" value="," placeholder="必填">
                    </div>
                </div>
               <div class="form-group">
                <label class="control-label col-sm-2">请选择CSV文件：</label>
                <div class="col-sm-6">
                    <input class="form-control" type="file" name="file" id="uploadFile" />
                </div>
               </div>
            </form>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered" id="userLists">
                        <tr>
                            <th>账户</th>
                            <th>昵称</th>
                            <th>密码</th>
                            <th>小组名</th>
                            <th>座位号</th>
                            <th>删除</th>
                        </tr>
                    </table>
                </div>
            </div>
            <button class="btn btn-success btn-block navbar-fixed-bottom btn-sm" type="button" id="save">保存</button>
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
                    <button class="btn btn-primary btn-sm" type="button" data-dismiss="modal" id="confirm">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {

            var delimiter = $("#delimiter").val();

            $("#delimiter").change(function () {
                delimiter = $(this).val();
                window.location.reload();
            })

            $("#uploadFile").AjaxFileUpload({
                arg:delimiter,
                action: "{{ URL('admin/user/upload_user?_token='.csrf_token()) }}",
                onComplete: function(filename, response) {
                    var str = response.replace(/<pre>/, '');
                    str = str.replace(/<\/pre>/, '');
                    response = eval("("+str+")");

                    if(response['status'] == false) {
                        $("#info").html(response['info']);
                        $("#alert").modal();
                    } else {
                      //  $("#userLists").children().gt(1).remove();
                        for(var i = 0; i < response['res'].length; i++)
                        {
                            var str = '<tr>';
                            for(var j = 0; j < response['res'][i].length; j++) {
                                str += '<td>' + response['res'][i][j] + '</td>';
                            }
                            str += '<td><img style="cursor:pointer" src={{ URL('image/trash.png') }} width="15px" /></td>'

                            $("#userLists").append(str + '</tr>');
                        }
                    }
                }
            });

            $("#userLists").on('dblclick', 'td', function(){
                $("#userLists td").each(function(){
                    if($(this).html().indexOf("form-control") != -1)
                        $(this).html($(this).children().eq(0).val());
                })
                if($(this).html().indexOf("img") == -1) {
                    var str = '<input type="text" class="form-control" value="' + $(this).html() + '"/>';
                    $(this).html(str);
                }
            })

            $("#userLists").on('click', 'img', function(){
                $(this).parent().parent().remove();
            })

            $("#userLists").on('keypress', 'input', function (e) {
                if(e.keyCode == 13) {
                    $("#userLists td").each(function() {
                        if ($(this).html().indexOf("form-control") != -1)
                            $(this).html($(this).children().eq(0).val());
                    })
                   /* var str = $(this).parent().html();

                    str = str.substr(str.indexOf("value=")+7);
                    str = str.substr(0, str.indexOf('"'));
                    alert(str);
                    $(this).parent().html(str);*/
                }
            })

            $("#save").click(function(){

                $("#userLists td").each(function(){
                    if($(this).html().indexOf("form-control") != -1)
                        $(this).html($(this).children().eq(0).val());
                })

                var users = new Array();
                $("#userLists tr").each(function (index, target) {
                    var row = new Array();
                    if(index >= 1) {
                        $(target).children().each(function(index, target){
                            if(index <= 4) {
                                row.push($(this).html());

                            }
                        })
                        users.push(row);
                    }
                })
                $.post("{{ URL('admin/user/do_import') }}", {data:users, _token:"{{ csrf_token() }}"}, function(res){
                    if(res['status'] == true) {
                        window.location.href='{{ URL('/admin/team/show') }}';
                    } else {
                       /* $("#info").html(res['info']);
                        $("#info").modal();*/
                    }
                })
            })
        })
    </script>
@endsection