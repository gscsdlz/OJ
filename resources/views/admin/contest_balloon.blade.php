@extends('admin.layout')
@section('title')
    气球分发
@endsection
@section('main')
    <hr/>
    <div class="row">
        <div class="col-md-3">
            <ol class="text-danger">
                <li>当前页面请不要使用浏览器的刷新按钮！！！或者是键盘的F5功能键！！！该页面请使用右侧手动刷新按钮或者等待自动刷新</li>
                <li>建议比赛开始和结束前，提交量大时使用手动刷新；其余时候提交量小，点击按钮手动刷新</li>
            </ol>
        </div>
        <div class="col-md-6 text-center">
            <form class="form-inline">
                <div class="form-group">
                    <label class="control-label">关注的比赛</label>
                        <select class="form-control" id="contest_select">
                            <option value="-1">请选择</option>
                            @foreach($lists as $c)
                                <option value="{{ $c->contest_id }}">{{ $c->contest_name }}</option>
                            @endforeach
                        </select>
                </div>
                <div class="form-group">
                    &nbsp;<label><input type="checkbox" id="autoRefresh" onclick="if($(this).prop('checked')) flag = true; else flag = false"/> 1分钟自动刷新</label>
                </div>
                <div class="form-group">
                    &nbsp;<label><input type="checkbox" id="hiddenSeat" onclick="if($(this).prop('checked')) hiddenFlag = true; else hiddenFlag = false;get_balloon(true)"/> 不显示无座位号提交</label>
                </div>
            </form>
            <hr/>
            <button class="btn btn-primary" type="button" onclick="get_balloon(true)">点我即可手动刷新</button>
            <hr/>
            <table class="table table-hover table-bordered" id="submits">
                <tr>
                    <th>提交记录号</th>
                    <th>题目编号</th>
                    <th>用户名-昵称</th>
                    <th>座位号</th>
                    <th>提交时间</th>
                    <th>送出气球</th>
                </tr>
            </table>
        </div>
    </div>
    <script>
        var token = "{{ csrf_token() }}";
        var flag = false;
        var hiddenFlag = false;
        $(document).ready(function(){

            window.setInterval('get_balloon(flag)', 6000);

            $("#contest_select").change(function(){
                get_balloon(true);
            })

        })

        function get_balloon(f) {
            if(f == false)
                return;
            else {
                var id = $("#contest_select").val();
                hiddenFlag = hiddenFlag == true ? 1 : 0;
                if(id != 0)
                $.post("{{  URL('admin/get_balloon') }}", {cid:id, hidden:hiddenFlag, _token:token}, function(data){
                    $("#submits tr:gt(0)").remove();
                    if(data.status == true) {
                        for(var i = 0; i < data['list'].length; i++)
                            $("#submits").append('<tr>' +
                                '<td>'+data['list'][i]['submit_id']+'</td>' +
                                '<td>'+data['list'][i]['inner_id']+'</td>' +
                                '<td>'+data['list'][i]['username']+'('+data['list'][i]['nickname']+')</td>' +
                                '<td>'+data['list'][i]['seat']+'</td>' +
                                '<td>'+getTime(data['list'][i]['submit_time'])+'</td>'+
                                '<td><img onclick="send_balloon($(this), '+data['list'][i]['submit_id']+')" style="cursor:pointer" src="{{ URL('image/balloon.png') }}" width="20px"/></td>' +
                                '</tr>')
                    }
                })
            }
        }

        function send_balloon(target, id) {
            $.post("{{ URL('admin/send_balloon') }}", {sid:id, _token:token}, function(data){
                $(target).parent().parent().remove();
            })
        }

        function getTime(/** timestamp=0 **/) {
            var ts = arguments[0] || 0;
            var t,y,m,d,h,i,s;
            t = ts ? new Date(ts*1000) : new Date();
            y = t.getFullYear();
            m = t.getMonth()+1;
            d = t.getDate();
            h = t.getHours();
            i = t.getMinutes();
            s = t.getSeconds();
            // 可根据需要在这里定义时间格式
            return y+'-'+(m<10?'0'+m:m)+'-'+(d<10?'0'+d:d)+' '+(h<10?'0'+h:h)+':'+(i<10?'0'+i:i)+':'+(s<10?'0'+s:s);
        }
    </script>
@endsection