@extends('admin.layout')
@section('title')
    @if(isset($contest)){{ $contest->contest_name }}@endif
@endsection
@section('main')
    <hr/>
<div class="row">
    <div class="col-md-6 col-md-offset-3 well">
        <form class="form-horizontal">
            <div class="form-group">
                <label for="contest_name" class="col-sm-4">比赛名称</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="contest_name" placeholder="请输入比赛名称" value="@if(isset($contest)){{ $contest->contest_name }}@endif">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-4">奖牌设置</label>
            </div>
            <div class="form-group">
                <label class="col-sm-6 col-sm-offset-1">金牌（一等奖）数量</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" id="auNum" placeholder="金牌（一等奖）数量" value="@if(isset($contest)) {{ $contest->au }}@endif">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-6 col-sm-offset-1">银牌（二等奖）数量</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" id="agNum" placeholder="银牌（二等奖）数量" value="@if(isset($contest)) {{ $contest->ag }}@endif">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-6 col-sm-offset-1">铜牌（三等奖）数量</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" id="cuNum" placeholder="铜牌（三等奖）数量" value="@if(isset($contest)) {{ $contest->cu }}@endif">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-6 col-sm-offset-1">铁牌（优秀奖）数量</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" id="feNum" placeholder="铁牌（优秀奖）" value="@if(isset($contest)) {{ $contest->fe }}@endif">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-4">比赛模式</label>
                <div class="col-sm-8">
                    <select class="form-control" id="options">
                        <option value="0" @if(isset($contest) && $contest->options == 0) selected @endif>普通模式</option>
                        <option value="1" @if(isset($contest) && $contest->options == 1) selected @endif>关闭排名</option>
                        <option value="2" @if(isset($contest) && $contest->options == 2) selected @endif>禁止查看自己的代码</option>
                        <option value="3" @if(isset($contest) && $contest->options == 3) selected @endif>关闭排名且禁止查看自己代码</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-4">赛制设置</label>
                <div class="col-sm-8">
                    <label><input type="radio" name="oi" onclick="score_change(false)" value="0" @if(!isset($contest) || $contest->oi == 0) checked @endif/> ACM赛制</label>
                    <label><input type="radio" name="oi" onclick="score_change(true)" value="1" @if(isset($contest) && $contest->oi == 1) checked @endif/> OI赛制</label>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label for="" class="col-sm-4">比赛权限</label>
                <div class="col-sm-6">
                    <label><input type="radio" onclick="$('#teamManager').hide()" name="contest_pass" value="1" @if(!isset($contest) || $contest->contest_pass == 1) checked @endif/> 公开比赛</label>
                    <label><input type="radio" onclick="$('#teamManager').show()" name="contest_pass" value="2" @if(isset($contest) && $contest->contest_pass == 2) checked @endif/> 私有比赛（指定小组可以参与）</label>
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#TeamModal" id="teamManager">小组管理</button>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label class="col-sm-4">比赛开始时间</label>
                <div class="col-sm-8">
                </div>
            </div>
            <div class="form-group" id="c_stime">
                <div class="col-sm-2">
                    <select class="form-control" onchange="change_days($(this).parent().next().children().eq(0))">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" onchange="change_days($(this))">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control">
                    </select>
                </div>
            </div>
            <hr/>
            <div class="form-group">
                <label for="c_etime" class="col-sm-4">比赛结束时间
                </label>
                <div class="col-sm-8">
                </div>
            </div>
            <div class="form-group" id="c_etime">
                <div class="col-sm-2">
                    <select class="form-control" onchange="change_days($(this).parent().next().children().eq(0))">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control" onchange="change_days($(this))">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control">
                    </select>
                </div>
                <div class="col-sm-2">
                    <select class="form-control">
                    </select>
                </div>
            </div>
            <hr/>
            <label for="contest_title">比赛题目列表 <br/><span class="text-danger">双击题目实际ID
                即可删除当前题目 已经在比赛中的题目，如果删除会一并删除相关的提交数据，请注意！</span></label>
            <div id="prolist">
                @if (isset ( $proLists ))
                    @foreach($proLists as $p)
                        <div class="form-group">
                            <div class="col-sm-3">
                                <input value="{{ $p->inner_id }}" type="text" class="form-control"/>
                            </div>
                            <div class="col-sm-3">
                                <input readonly="true" ondblclick="delete_pro($(this).parent().parent())" value="{{ $p->pro_id }}" type="text" class="form-control" />
                            </div>
                            <div class="col-sm-3">
                                <label><a href="{{ URL('/problem/show/' . $p->pro_id) }}" target="_blank">{{ $p->pro_title }}</a></label>
                            </div>
                            <div class="col-sm-3">
                                @if($contest->oi == 0)
                                    <input value="" type="text" placeholder="ACM赛制下分数无效" class="form-control" readonly/>
                                @else
                                    <input value="{{ $p->max_score }}" type="text" class="form-control"/>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="form-group">
                <div class="col-sm-4">
                    <input id="pro_id" value="" type="text" class="form-control"
                           placeholder="请填写题目实际编号" />
                </div>
                <div class="col-sm-4">
                    <label><a id="pro_title" href="javascript:void(0)">请填写题目实际编号</a></label>
                </div>
            </div>
            <div class="form-group text-center">
                <button type="button" style="width: 100px"
                        class="btn btn-primary" id="add">确认添加</button>
            </div>
            <hr />
            <p class="text-danger">没有点击保存之前，所做的操作不会同步到数据库中，请不要离开该页面或者刷新页面</p>
            <div class="form-group text-center">
                <button type="button" class="btn btn-success btn-block" id="save">保存</button>
            </div>
            <p class="text-danger" id="info"></p>
        </form>
    </div>
</div>
    <div class="modal fade" id="TeamModal" tabindex="-1" role="dialog" aria-labelledby="TeamModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="TeamModalLabel">小组管理</h4>
                </div>
                <div class="modal-body">
                    <p class="text-danger">点击&raquo;和&laquo;即可调整小组，左侧小组为候选小组，右侧小组为最终小组，点击确认并不会保存进数据库，必须点击页面中的保存按钮才可以。</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="list-group" id="list1">
                                @foreach($teamLists as $t)
                                    <li class="list-group-item"><span>{{ $t->team_name }}</span><span style="cursor: pointer" class="badge" onclick="make_change(0, $(this))">&raquo;</span><span style="display: none;">{{ $t->team_id }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <ul class="list-group" id="list2">
                                @if(isset($cteams))
                                    @foreach($cteams as $t)
                                        <li class="list-group-item"><span>{{ $t->team_name }}</span><span style="cursor: pointer" class="badge" onclick="make_change(1, $(this))">&laquo;</span><span style="display: none;">{{ $t->team_id }}</span></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">确定</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        var pro_id;
        var pro_title;
        var valid_id = false;
        var prolist = new Array();
        var token = "{{ csrf_token() }}";

        @if(isset($contest))
        var y = new Array( {{ date('Y', $contest->c_stime) }}, {{ date('Y', $contest->c_etime) }});
        var m = new Array( {{ date('m', $contest->c_stime) }}, {{ date('m', $contest->c_etime) }});
        var d = new Array( {{ date('d', $contest->c_stime) }}, {{ date('d', $contest->c_etime) }});
        var h = new Array( {{ date('H', $contest->c_stime) }}, {{ date('H', $contest->c_etime) }});
        var M = new Array( {{ date('i', $contest->c_stime) }}, {{ date('i', $contest->c_etime) }});
        var s = new Array( {{ date('s', $contest->c_stime) }}, {{ date('s', $contest->c_etime) }});

        var hasTime = true;
        @else
           var hasTime = false;
        @endif

        $(document).ready(function() {
            make_uniqu();
           /* $(window).bind('beforeunload', function () {
                return '您输入的内容尚未保存，确定离开此页面吗？';
            });*/

            @if(isset($contest) && $contest->contest_pass == 2)
                $("#teamManager").show();
            @else
                $("#teamManager").hide();
            @endif

            $("#pro_id").keyup(function () {
                valid_id = false;
                pro_id = parseInt($(this).val());
                if (pro_id >= 1000) {
                    $.post("{{ URL('/admin/contest/pro_check') }}", {pro_id: pro_id, _token:token}, function (arr) {
                        if (arr['status']) {
                            valid_id = true;
                            pro_title = arr['pro_title'];
                            $("#pro_title").html(arr['pro_title']);
                            $("#pro_title").attr("href", "{{ URL('/problem/show/') }}" + '/' + pro_id);
                            $("#pro_title").attr("target", "_blank");
                        } else {
                            $("#pro_title").html("题目ID不合法");
                            $("#pro_title").attr("href", "javascript:void(0)");
                            $("#pro_title").removeAttr("target");
                        }
                    })
                }
            })
            $("#add").click(function () {
                if (valid_id) {
                    var lastId = parseInt($("#prolist").children().last().children().eq(0).children().eq(0).val());
                    if (isNaN(lastId))
                        lastId = 1000;
                    else
                        lastId += 1;
                    $("#prolist").append('' +
                        '<div class="form-group">' +
                            '<div class="col-sm-3">' +
                                '<input placeholder="请输入题目的比赛编号" value="' + lastId + '" type="text" class="form-control"/>' +
                            '</div>' +
                            '<div class="col-sm-3">' +
                                '<input readonly="true" ondblclick="delete_pro($(this).parent().parent())" value="' + pro_id + '" type="text" class="form-control" />' +
                            '</div>' +
                            '<div class="col-sm-3">' +
                                '<label><a href="{{ URL('problem/show') }}' + '/' + pro_id + '" target="_blank">' + pro_title + '</a></label>' +
                            '</div>' +
                            '<div class="col-sm-3">' +
                                '<input placeholder="" value="100" type="text" class="form-control"/>' +
                            '</div>' +
                        '</div>'
                    );

                    $("#pro_id").val("");
                    $("#pro_title").html("请填写题目实际编号");
                    $("#pro_title").attr("href", "javascript:void(0)");
                    $("#pro_title").removeAttr("target");
                }
            })
            $("#save").click(function () {

                $("h5").remove();
                var ok = true;
                var contest_name = $("#contest_name").val();
                var contest_pass = $("input[name='contest_pass']:checked").val();
                var oi = $("input[name='oi']:checked").val();
                var c_stime = get_timestamp($("#c_stime"))
                var c_etime = get_timestamp($("#c_etime"))
                var auNum = parseInt($("#auNum").val());
                var agNum = parseInt($("#agNum").val());
                var cuNum = parseInt($("#cuNum").val());
                var feNum = parseInt($("#feNum").val());
                var options = $("#options").val();
                var teams = new Array();

                $("#list2").children().each(function(){
                    teams.push($(this).children().eq(2).html());
                })
                $("#prolist div.form-group").each(function (index) {
                    var inner_id = $(this).children().eq(0).children().eq(0).val();
                    var pro_id = $(this).children().eq(1).children().eq(0).val();
                    var max_score = $(this).children().eq(3).children().eq(0).val();

                    if (inner_id < 1000) {
                        $(this).attr("class", "form-group has-error");
                        $(this).append('<h5 class="text-danger">比赛中的题目ID不合法</h5>');
                        ok = false;
                    }
                    prolist[index] = new Array(inner_id, pro_id, max_score);
                })
                if (ok) {
                    $("#info").html("保存中，请稍后！");
                    $.post("{{ URL('/admin/contest/save') }}", {
                        @if (isset($contest))
                        contest_id:{{ $contest->contest_id }},
                        @endif
                        contest_name: contest_name,
                        contest_pass: contest_pass,
                        c_stime: c_stime,
                        c_etime: c_etime,
                        prolist: prolist,
                        auNum: auNum,
                        agNum: agNum,
                        cuNum: cuNum,
                        feNum: feNum,
                        oi:oi,
                        options: options,
                        teams:teams,
                        _token:token,
                    }, function (data) {
                        if (data['status']) {
                            $(window).unbind('beforeunload');
                            window.location.href = "{{ URL('/admin/contest/edit/') }}" + '/' +data['contest_id'];
                        } else {
                            $("#info").html("错误，请重试");
                        }
                    });
                }
            })

            update_date_time($("#c_stime"), 0);
            update_date_time($("#c_etime"), 1);
        })


        function delete_pro(pro_dom) {
            $(pro_dom).remove();
        }

        function  make_uniqu() {
            var arr = new Array()
            $("#list2").children().each(function(){
                arr.push($(this).children().eq(0).html());
            })

            $("#list1").children().each(function(){
                if(arr.indexOf($(this).children().eq(0).html()) != -1) {
                    $(this).remove();
                }
            })
        }

        function make_change(flag, target) {
            var tname = $(target).prev().html();
            var tid = $(target).next().html();
            if(flag == 0) {
                $("#list2").append('<li class="list-group-item"><span>' + tname+ '</span><span style="cursor: pointer" class="badge" onclick="make_change(1, $(this))">&laquo;</span><span style="display: none;">' + tid + '</span></li>');
            } else {
                $("#list1").append('<li class="list-group-item"><span>' + tname+ '</span><span style="cursor: pointer" class="badge" onclick="make_change(0, $(this))">&raquo;</span><span style="display: none;">' + tid + '</span></li>');
            }
            $(target).parent().remove();
        }

        function update_date_time(target, pos)
        {
            for(var i = {{ date('Y', time()) }}; i < 2037; i++) {
                $(target).children().eq(0).children().eq(0).append('<option value="' + i + '">' + i + '年</option>')
            }

            for(var i = 1; i <= 12; i++)
                $(target).children().eq(1).children().eq(0).append('<option value="'+i+'">'+i+'月</option>')

            for(var i = 1; i <= 31; i++)
                $(target).children().eq(2).children().eq(0).append('<option value="'+i+'">'+i+'日</option>')

            for(var i = 0; i < 24; i++) {
                var k = i < 10 ? '0' + i : i;
                $(target).children().eq(3).children().eq(0).append('<option value="' + i + '">' + k + '</option>')
            }

            for(var i = 0; i < 60; i++) {
                var k = i < 10 ? '0' + i : i;
                $(target).children().eq(4).children().eq(0).append('<option value="' + i + '">' + k + '</option>')
                $(target).children().eq(5).children().eq(0).append('<option value="' + i + '">' + k + '</option>')
            }

            if(hasTime == true) {
                $(target).children().eq(0).children().eq(0).val(y[pos]);
                $(target).children().eq(1).children().eq(0).val(m[pos]);
                $(target).children().eq(2).children().eq(0).val(d[pos]);
                $(target).children().eq(3).children().eq(0).val(h[pos]);
                $(target).children().eq(4).children().eq(0).val(M[pos]);
                $(target).children().eq(5).children().eq(0).val(s[pos]);
            }
        }

        //确保闰年和其余月份的30 / 31号合法性
        function change_days(target) {
            var year = $(target).parent().prev().children().eq(0).val();
            var month = parseInt($(target).val());
            $(target).parent().next().children().eq(0).html("");
            var maxM = 0;
            if(month != 2) {
                maxM = (month == 1 || month == 3 || month == 5 || month == 7 || month == 8  || month == 10 || month == 12) ? 31 : 30;
            } else {
                if(year % 4 == 0 && year % 100 != 0 || year % 400 == 0)
                    maxM = 29;
                else
                    maxM = 28;
            }
            for(var i = 1; i <= maxM; i++)
                $(target).parent().next().children().eq(0).append('<option value="'+i+'">'+i+'日</option>')
        }

        function get_timestamp(target) {
            var d = new Date();
            d.setFullYear($(target).children().eq(0).children().eq(0).val());
            d.setMonth($(target).children().eq(1).children().eq(0).val() - 1);
            d.setDate($(target).children().eq(2).children().eq(0).val());
            d.setHours($(target).children().eq(3).children().eq(0).val());
            d.setMinutes($(target).children().eq(4).children().eq(0).val());
            d.setSeconds($(target).children().eq(5).children().eq(0).val());
            return parseInt(d.getTime() / 1000);
        }

        //控制分数表单是否有效
        function score_change(flag) {
            $("#prolist").children().each(function(){
                if(flag == true) {
                    $(this).children().eq(3).children().eq(0).val(0);
                    $(this).children().eq(3).children().eq(0).attr('readonly', false);
                } else {
                    $(this).children().eq(3).children().eq(0).val("");
                    $(this).children().eq(3).children().eq(0).attr("placeholder", "ACM赛制下分数无效");
                    $(this).children().eq(3).children().eq(0).attr('readonly', true);
                }

            })

        }
    </script>

@endsection