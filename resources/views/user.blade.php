@extends('layout')
@section('title')
    个人中心
@endsection
@section('main')
<div class="row">
    <div class="col-md-2 col-md-offset-2 text-center well"
         style="border-right-style: inset">
        <img src="{{ URL('image/header/'.$userInfo->headerpath) }}" alt="Header" class="img-rounded" width="80%" id="header">
        @if(Session::has('username') && Session::get('username') == $userInfo->username)
        <form id="uploadImg" class="form-horizontal" role="form" method="post" action="" enctype="multipart/form-data">
            <label>请选择图片文件：<input class="form-control" type="file" name="file" id="uploadFile" /></label>
        </form>
        <script>
            $(document).ready(function() {
                $("#uploadImg").hide();
                $("#header").dblclick(function(){
                    $("#uploadImg").show();
                })

                $("#uploadFile").AjaxFileUpload({
                    action: "{{ URL('/user/uploadHeader') }}",
                    onComplete: function(filename, response) {
                        window.location.reload();
                        /*var arg = eval(response);
                         $("#header").attr("src", "\\Src\\Image\\header\\" + arg['status']);
                         $("#uploadImg").hide();*/
                    }
                });
            });
        </script>
        @endif
        <h1>{{ $userInfo->username }}
            <small>{{ $userInfo->nickname }}</small>
        </h1>
        <h3>
            <small>{{ $userInfo->motto }}</small>
        </h3>
        @if(Session::has('username') && Session::get('username') == $userInfo->username)
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <button type="button" class="btn btn-primary btn-block"
                        data-toggle="modal" data-target="#updateModal">修改信息</button>
            </div>
        </div>
        <div class="modal fade text-left" id="updateModal" tabindex="-1"
             role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                        </button>
                        <h2 class="text-center modal-title" id="codeModalLabel">修改用户信息</h2>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label for="Username" class="col-sm-2 control-label">用户名</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="Username" readonly  value="{{ $userInfo->username }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Nickname" class="col-sm-2 control-label">昵称</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="Nickname" @if($userInfo->activate == 0) readonly @endif placeholder="昵称" value="{{ $userInfo->nickname }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Motto" class="col-sm-2 control-label">签名</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="Motto" placeholder="签名，最大字数不超过30个汉字" value="{{ $userInfo->motto }}">
                                    <label id="mottoError" class="control-label text-danger">签名超过最大字数</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="QQ" class="col-sm-2 control-label">QQ</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="QQ" placeholder="QQ号" value="{{ $userInfo->qq }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="Group" class="col-sm-2 control-label">小组</label>
                                <div class="col-sm-10">
                                    @if($teamInfo->private == 1 || $userInfo->activate == 0)
                                        <input type="text" class="form-control" value="{{ $teamInfo->team_name }}-私有小组不可更换" readonly>
                                    @else
                                        <select id="Group" class="form-control">
                                            @foreach($teams as $row)
                                                @if($row['private'] == 0)
                                                    @if($row['team_name'] == $teamInfo->team_name)
                                                        <option selected value="{{ $row['team_id'] }}">{{ $row['team_name'] }}</option>
                                                    @else
                                                        <option value="{{ $row['team_id'] }}">{{ $row['team_name'] }}</option>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </select>
                                    @endif

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="seat" class="col-sm-2 control-label">座位号</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="Seat" @if($userInfo->activate == 0)readonly="readonly"@endif value="{{ $userInfo->seat }}">
                                    <label id="seatRegError" class="control-label text-danger">座位号格式应该为123_45或者为空</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email" class="col-sm-2 control-label">电子邮箱</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="Email"  placeholder="请输入电子邮件" value="{{ $userInfo->email }}">
                                    <label id="emailError" class="control-label text-danger">该电子邮箱已经被注册过了！</label>
                                    <label id="emailRegError" class="control-label text-danger">邮箱格式不正确！</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">头像</label>
                                <div class="col-sm-10">
                                    <label class="control-label text-success">头像请双击图片修改</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-sm-2 control-label">密码</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="Password" name="newPassword" placeholder="输入密码则表示修改密码">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password2" class="col-sm-2 control-label">确认密码</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="Password2" name="newPassword2" placeholder="确认密码">
                                    <label id="passwordError" class="control-label text-danger">两次输入的密码不一致</label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                        <button type="button" class="btn btn-primary" id="update">修改</button>
                    </div>
                    <p class="text-right text-danger" id="updateError">更新失败，请重试</p>
                </div>
            </div>
        </div>
        @endif
        <hr />
        <table class="table text-left">
            <tr>
                <td>所在小组</td>
                <td>{{ $teamInfo->team_name }}</td>
            </tr>
            <tr>
                <td>座位号</td>
                <td>{{ $userInfo->seat }}</td>
            </tr>

            <tr>
                <td>加入时间</td>
                <td>{{  $userInfo->regtime }}</td>
            </tr>
            <tr>
                <td>上次登录时间</td>
                <td>{{  date('Y-m-d H:i:s', $userInfo->lasttime) }}</td>
            </tr>
            @if(Session::has('username') && Session::get('username') == $userInfo->username)
            <tr>
                <td>上次登录地点</td>
                <td id="lastaddr"></td>
            </tr>
            @endif
            <tr>
                <td>QQ</td>
                <td>{{  $userInfo->qq }}</td>
            </tr>
            <tr>
                <td>电子邮箱</td>
                <td><a href="mailto:{{  $userInfo->email }}">{{  $userInfo->email }}</a></td>
            </tr>

        </table>
    </div>

    <div class="col-md-3 well" id="AllStatus" style="height: 335px; margin-left: 10px;"></div>
    <div class="col-md-3" style=" margin-left: 10px;">
        <div class=" panel panel-success">
            <div class="panel-heading">
                <h4 class="text-center">附近的人</h4>
            </div>
            <div class="panel-body">
                <table class="table table-bo" id="rank">
                    <tr>
                        <th>排名</th>
                        <th>用户名</th>
                        <th>通过数</th>
                        <th>总提交数</th>
                        <th>AC率</th>
                    </tr>
                    <tr>
                        <td>&nbsp;</td><td></td><td></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td><td></td><td></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td><td></td><td></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td><td></td><td></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td><td></td><td></td><td></td><td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6" style="margin-left: 15px;" id="contestList">
        <div class="col-md-10 col-md-offset-4">
            <button class="btn btn-danger" data-toggle="modal" data-target="#ACModal">已经解决的问题 <span class="badge">{{ count($acList) }}</span></button>
            <button class="btn btn-success" data-toggle="modal" data-target="#nACModal">还未解决的问题 <span class="badge">{{ count($waList) }}</span></button>
        </div>
        <hr/>
        <div class=" panel panel-danger">
            <div class="panel-heading">
                <h4 class="text-center">参加过的比赛</h4>
            </div>
            <div class="list-group panel-body">
                @if(count($contestLists) != 0)
                    @foreach($contestLists as $row)
                        <a href="{{ URL('contest/show/'.$row->contest_id) }}" class="list-group-item">{{ $row->contest_name }}<span class="badge">{{ $row->sum }}</span></a>
                    @endforeach
                @else
                    <a href="#" class="list-group-item text-center">还用户目前还未参加比赛</a>
                @endif
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="ACModal" tabindex="-1" role="dialog"
     aria-labelledby="ACModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title">已经解决的问题</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    {{--*/ $i = 1; /*--}}
                    @foreach($acList as $row)
                        @if($i++ == 1)
                            <tr>
                        @endif
                                <td><a href="{{ URL('status') }}?rid=&pid={{ $row['pro_id'] }}&user={{ $userInfo->username }}&lang=0&status=4">{{ $row['pro_id'] }}</a></td>
                        @if($i == 11)
                            </tr>
                        {{--*/ $i = 1; /*--}}
                        @endif
                    @endforeach
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="nACModal" tabindex="-1" role="dialog" aria-labelledby="nACModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">关闭</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">还未解决的问题</h4>
            </div>
            <div class="modal-body">
                <table class="table">
                    {{--*/ $i = 1; /*--}}
                    @foreach($waList as $row)
                        @if($i++ == 1)
                            <tr>
                        @endif
                                <td><a href="{{ URL('status') }}?rid=&pid={{ $row['pro_id'] }}&user={{ $userInfo->username }}&lang=0">{{ $row['pro_id'] }}</a></td>
                        @if($i == 11)
                            </tr>
                            {{--*/ $i = 1; /*--}}
                        @endif
                    @endforeach
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        @if(Session::has('username') && Session::get('username') == $userInfo->username)
            $.post("{{ URL('user/get_ip_addr') }}", {_token:"{{ csrf_token() }}",ip_addr:"{{ $userInfo->lastip }}"}, function(data){
                $("#lastaddr").html(data['addr']);
            })


        $("#mottoError").hide();
        $("#emailError").hide();
        $("#emailRegError").hide();
        $("#passwordError").hide();
        $("#usernameError").hide();
        $("#usernameEmptyError").hide();
        $("#usernameRegError").hide();
        $("#seatRegError").hide();
        $("#updateError").hide();

        $("#update").click(function(){
            $("#mottoError").hide();
            $("#emailError").hide();
            $("#emailRegError").hide();
            $("#passwordError").hide();
            $("#usernameError").hide();
            $("#usernameEmptyError").hide();
            $("#usernameRegError").hide();
            $("#seatRegError").hide();
            $("#updateError").hide();

            var nickname = $("#Nickname").val();
            var motto = $("#Motto").val();
            var qq = $("#QQ").val();
            var email = $("#Email").val();
            var group = $("#Group").val();
            if(typeof(group) == 'undefined')
                group = -1;
            var password= $("#Password").val();
            var password2 = $("#Password2").val();
            var seat = $("#Seat").val();
            if(password != password2)
                $("#passwordError").show();
            else if(motto.length > 30)
                $("#mottoError").show();
           // else if(!(/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/.test(email)))
           //     $("#emailRegError").show();
            else
                $.post("{{ URL('user/update') }}", {_token:"{{ csrf_token() }}", nickname:nickname, motto:motto, qq:qq, seat:seat, email:email, group:group, password:password, password2:password2}, function(arr){
                    if (arr.status == true) {
                        window.location.reload();
                    } else if(arr.info == 'passError'){
                        $("#passwordError").show();
                    } else {
                        $("#updateError").show();
                    }
                })
        })
        @endif

        $.post("{{ URL('rank/nearby/'.$userInfo->username) }}", {_token:"{{ csrf_token() }}"}, function(data){
            $("#rank").html("<tr> <th>排名</th> <th>用户名</th> <th>通过数</th> <th>总提交数</th> <th>AC率</th> </tr>")
            for(var i = 0; i < data['res'].length; i++) {
                var str = '<tr><td>'+ data['res'][i][5]+'</td><td><a href="{{ URL('user/show/') }}/'+data['res'][i][0]+'">'+ data['res'][i][0]+'</td><td>'+ data['res'][i][3]+'</td><td>'+ data['res'][i][4]+'</td>';
                if (data['res'][i][4] == 0)
                    data['res'][i][4] = 1;
                str += '<td>' + parseInt(data['res'][i][3] / data['res'][i][4] * 100)+'%</td></tr>';
                $("#rank").append(str);
            }
          //  alert($("#rank").parent().parent().parent().css('height'));
          //  $("#AllStatus").css('height', $("#rank").parent().parent().parent().css('height'));
        })

    })
</script>

<script type="text/javascript">

    // 基于准备好的dom，初始化echarts实例
    var myChartAllStatus = echarts.init(document
        .getElementById('AllStatus'));
    optionA = {
        title: {
            text: '提交记录统计 总计{{ array_sum($submits) }}次',
            left: 'center'
        },
        tooltip : {
            trigger : 'item',
            formatter : "{a} <br/>{b} : {c} ({d}%)"
        },
        legend : {
            orient : 'vertical',
            left : 'left',
            data : [ 'AC', 'PE', 'WA', 'RE', 'TLE', 'MLE', 'OLE', 'CE' ]
        },
        series : [ {
            name : '题数及百分比',
            type : 'pie',
            radius : [ '50%', '70%' ],
            avoidLabelOverlap : false,
            label : {
                normal : {
                    show : false,
                    position : 'center'
                },
                emphasis : {
                    show : true,
                    textStyle : {
                        fontSize : '30',
                        fontWeight : 'bold'
                    }
                }
            },
            labelLine : {
                normal : {
                    show : false
                }
            },
            data : [
                {value : {{ $submits[0] }}, name : 'AC'},
                {value : {{ $submits[1] }}, name : 'PE'},
                {value : {{ $submits[2] }}, name : 'WA'},
                {value : {{ $submits[3] }}, name : 'RE'},
                {value : {{ $submits[4] }}, name : 'TLE'},
                {value : {{ $submits[5] }}, name : 'MLE'},
                {value : {{ $submits[6] }}, name : 'OLE'},
                {value : {{ $submits[7] }}, name : 'CE'},
            ]
        } ]
    };

    myChartAllStatus.setOption(optionA);
</script>
@endsection