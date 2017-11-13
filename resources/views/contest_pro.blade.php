@extends('layout')
@section('title'){{ $pro->pro_title }}@endsection
@section('main')
<nav class="col-md-3" style="">
    <ul class="list-group">
        <li class="list-group-item list-group-item-danger">
            <h4>{{ $contest->contest_name }}</h4>
            <p>开始时间：{{ date('Y-m-d H:i:s', $contest->c_stime) }}</p>
            <p>结束时间：{{ date('Y-m-d H:i:s', $contest->c_etime) }}</p>
            <p>当前时间：<span id="currTime">{{ date('Y-m-d H:i:s', time()) }}</span></p>

        </li>

        <li class="list-group-item">
            <div class="progress">
                <div id="percentNum" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
                    0%
                </div>
            </div>
        </li>
        <li class="list-group-item" id="info">
            <p class="text-danger" >这里将显示最新通知、公告！</p>
        </li>
        @foreach($proLists as $l)
                @if($pid == $l->inner_id)
                    <li class="list-group-item active">
                        @if($l->getACFlag() == 1)
                            <img src="{{ URL('image/ac.png') }}" width="20px" height="20px" style="float: right">
                        @elseif($l->getACFlag() == -1)
                            <img src="{{ URL('image/wa.png') }}" width="20px" height="20px" style="float: right">
                        @endif
                        <span class="badge">{{ $l->getAcNum() }}/{{ $l->getAllNum() }}</span>{{ $l->pro_title }}</li>
                @else
                    <li class="list-group-item">
                        @if($l->getACFlag() == 1)
                            <img src="{{ URL('image/ac.png') }}" width="20px" height="20px" style="float: right">
                        @elseif($l->getACFlag() == -1)
                            <img src="{{ URL('image/wa.png') }}" width="20px" height="20px" style="float: right">
                        @endif
                    <span class="badge">{{ $l->getAcNum() }}/{{ $l->getAllNum() }}</span><a href="{{ URL('contest/show/'.$cid.'/'.$l->inner_id)}}">{{ $l->pro_title }}</a></li>
                @endif
        @endforeach
    </ul>
</nav>
<div class="row">

    <div class="col-md-8">
    @if(isset($pro))
        <h1 class="text-center text-primary">{{ $pro->pro_title }}</h1>
        <h4 class="text-center text-danger">时间限制: {{ $pro->time_limit }}ms 内存限制: {{ $pro->memory_limit }}KB</h4>
        <h4 class="text-center text-danger">通过次数: <span class="badge">{{ $pro->getACNum() }}</span>
            总提交次数: <span class="badge">{{ $pro->getAllNum() }}</span></h4>
        @if($pro->getACFlag() == 1)
            <img src="{{ URL('image/ac.png') }}" width="50px" height="50px" style="float: right">
        @elseif($pro->getACFlag() == -1)
            <img src="{{ URL('image/wa.png') }}" width="50px" height="50px" style="float: right">
        @endif
        <div class="panel panel-default">
            <div class="panel-body">
                @if(!is_null($pro->pro_descrip))
                    <div class="panel panel-default">
                        <div class="panel-heading">问题描述</div>
                        <div class="panel-body">{!! $pro->pro_descrip !!}</div>
                    </div>
                @endif
                @if(!is_null($pro->pro_in))
                    <div class="panel panel-default">
                        <div class="panel-heading">输入描述</div>
                        <div class="panel-body">{!! $pro->pro_in !!}</div>
                    </div>
                @endif
                @if(!is_null($pro->pro_out))
                    <div class="panel panel-default">
                        <div class="panel-heading">输出描述</div>
                        <div class="panel-body">{!! $pro->pro_out !!}</div>
                    </div>
                @endif
                @if(!is_null($pro->pro_dataIn))
                    <div class="panel panel-default panel-danger">
                        <div class="panel-heading">样例输入</div>
                        <div class="panel-body"><pre>{!! $pro->pro_dataIn !!}</pre></div>
                    </div>
                @endif
                @if(!is_null($pro->pro_dataOut))
                    <div class="panel panel-default  panel-danger">
                        <div class="panel-heading">样例输出</div>
                        <div class="panel-body"><pre>{!! $pro->pro_dataOut !!}</pre></div>
                    </div>
                @endif
                @if(!is_null($pro->author))
                    <div class="panel panel-default">
                        <div class="panel-heading">来源</div>
                        <div class="panel-body">{!! $pro->author !!}</div>
                    </div>
                @endif
                @if(!is_null($pro->hint))
                    <div class="panel panel-default">
                        <div class="panel-heading">提示</div>
                        <div class="panel-body">{!! $pro->hint !!}</div>
                    </div>
                @endif
                <div class="panel panel-default">
                    <div class="panel-body text-center">
                        <button type="button" class="btn btn-danger btn-lg"
                                data-toggle="modal"
                                data-target="@if(Session::has('user_id'))
                                        #codeModal
                                    @else
                                        #signModal
                                    @endif">提交</button>
                    </div>
                </div>
            </div>
        </div>
        @else
        <h1 class="text-danger text-center">暂无任何题目</h1>
        @endif
    </div>

</div>
@if(!is_null(Session::get('user_id', null)))
    <div class="modal fade" id="codeModal" tabindex="-1" role="dialog"
         aria-labelledby="codeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="codeModalLabel">请选择适当的语言并粘贴代码</h4>
                </div>
                <div class="modal-body">
                    <form class="form-inline">
                        <div class="form-group">
                            <h4 id="submitCodeError" class="text-danger">提交错误，请刷新浏览器</h4>
                            @if(isset($pro))
                            <input type="text" readonly="readonly" class="form-control" value="{{ $pro->pro_title }}">
                            <input type="hidden" readonly="readonly" class="form-control" id="pid" value="{{ $pro->inner_id }}">
                            @endif
                            <select class="form-control" id="lang">
                                {{--*/ $langArr = config('web.langArr') /*--}}
                                @for($i = 0; $i < count($langArr); $i++)
                                    <option value="{{ $i }}">{{ $langArr[$i] }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>
                    <label id="missPidError" class="text-danger">题目编号为空或非法</label>
                    <p></p>
                    <div class="form-group">
                        <textarea class="form-control" rows="10" id="code"></textarea>
                        <label id="emptyCodeError" class="text-danger">代码为空</label>
                        <label id="codeCE" class="text-danger"></label>
                        <p></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" id="submitCode">提交</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){

            $("#missPidError").hide();
            $("#emptyCodeError").hide();
            $("#submitCodeError").hide();

            $("#code").keypress(function(){
                $("#submitCode").attr("class", "btn btn-primary");
                $("#codeCE").html("");
            })

            $("#submitCode").click(function(){

                $("#missPidError").hide();
                $("#emptyCodeError").hide();
                $("#submitCodeError").hide();
                var pid = $("#pid").val();
                var lang = $("#lang").val();
                var codes = $("#code").val();
                if($(this).attr("class") == 'btn btn-danger')
                    submit(pid, lang, codes);
                else {
                    var tmp = codes;
                    tmp = ClearBr(tmp);
                    tmp = trim(tmp);
                    tmp = CTim(tmp);


                    if (pid.length != 4)
                        $("#missPidError").show();
                    else if (codes.length == 0)
                        $("#emptyCodeError").show();
                    else if(lang == 0){
                        $("#codeCE").html("请选择合适的语言，下一次提交会默认选择该语言");
                        //$(this).attr("class", "btn btn-danger");
                    } else if ( tmp.indexOf("publicclassMain") == -1 && lang == 3) {//Java need public class Main
                        $("#codeCE").html("在你的Java代码中未发现public class Main，请检查，若确定无误，请继续点击提交！");
                        $(this).attr("class", "btn btn-danger");
                    } else if ( tmp.indexOf("package") != -1 && lang == 3) {//Java can't use package
                        $("#codeCE").html("在你的Java代码中发现package关键字，请检查，若确定无误，请继续点击提交！");
                        $(this).attr("class", "btn btn-danger");
                    } else if(tmp.indexOf("publicclass") != -1 && lang != 3) {  //C/C++ do not include public class
                        $("#codeCE").html("在你的C/C++代码中发现Java的代码，请选择正确的语言，若确定无误，请继续点击提交！");
                        $(this).attr("class", "btn btn-danger");
                    } else if((tmp.indexOf("iostream") != -1 || tmp.indexOf("usingnamespacestd;") != -1) && lang == 1){ //C can't use C++ header file
                        $("#codeCE").html("在你的C代码中发现C++的代码，请选择正确的语言，若确定无误，请继续点击提交！");
                        $(this).attr("class", "btn btn-danger");
                    } else if(tmp.indexOf('system("pause");') != -1){
                        $("#codeCE").html("在你的代码中发现system(\"pause\")，如果是注释，请继续点击提交！");
                        $(this).attr("class", "btn btn-danger");
                    } else if(tmp.indexOf("#include") != -1 && lang == 3) {//Java can't use #include
                        $("#codeCE").html("在你的Java代码中发现C/C++的代码，请选择正确的语言，若确定无误，请继续点击提交！");
                        $(this).attr("class", "btn btn-danger");
                    } else if(tmp.indexOf("intmain(") == -1 && lang != 3) { //C/C++ need int main() AND return 0;
                        $("#codeCE").html("在你的C/C++代码中未发现标准int main主函数，请检查，若确定无误，请继续点击提交！");
                        $(this).attr("class", "btn btn-danger");
                    } else if(tmp.indexOf("return0;") == -1 && lang != 3) { //C/C++ need int main() AND return 0;
                        $("#codeCE").html("在你的C/C++代码中未发现return 0;，请检查，若确定无误，请继续点击提交！");
                        $(this).attr("class", "btn btn-danger");
                    } else if((tmp.indexOf("%I64d") != -1 || tmp.indexOf("__int64") != -1) && lang != 3) {
                        $("#codeCE").html("64位整数请使用long long 和 %lld;，请检查，若确定无误，请继续点击提交！");
                        $(this).attr("class", "btn btn-danger");
                    }
                    else
                        submit(pid, lang, codes)
                }
            })

            function submit(pid, lang, codes) {
                $.post("{{ URL('submit') }}", {pro_id:pid, lang:lang, codes:codes,cid:{{ $cid }},_token:"{{csrf_token()}}"
                }, function(data){
                    if(data.status == true)
                        location.href="{{ URL('/status?cid='.$cid) }}"
                    else if(data.info == 'Time Error') {
                        $("#submitCodeError").html("比赛已经结束！");
                        $("#submitCodeError").show();
                    } else
                        $("#submitCodeError").show();
                })
            }
            $("textarea").on('keydown', function(e) {
                if (e.keyCode == 9) {
                    e.preventDefault();
                    var indent = '    ';
                    var start = this.selectionStart;
                    var end = this.selectionEnd;
                    var selected = window.getSelection().toString();
                    selected = indent + selected.replace(/\n/g, '\n' + indent);
                    this.value = this.value.substring(0, start) + selected
                        + this.value.substring(end);
                    this.setSelectionRange(start + indent.length, start
                        + selected.length);
                }
            })
        })
        //去除换行
        function ClearBr(key) {
            key = key.replace(/<\/?.+?>/g,"");
            key = key.replace(/[\r\n]/g, "");
            return key;
        }

        //去掉字符串两端的空格
        function trim(str) {
            return str.replace(/(^\s*)|(\s*$)/g, "");
        }

        //去除字符串中间空格
        function CTim(str) {
            return str.replace(/\s/g,'');
        }
    </script>
@endif
<script>

    function auto_event(){
        var unixnow = Date.parse(new Date($("#currTime").html())) / 1000;
        var stime = {{ $contest->c_stime }};
        var etime = {{ $contest->c_etime }};
        unixnow = unixnow + 1;

        $("#currTime").html(getTime(unixnow));
        var percent;
        if(unixnow < stime)
            percent = 0;
        else if(unixnow > etime)
            percent = 100;
        else
            percent = parseInt((unixnow - stime) / (etime - stime) * 100);

        $("#percentNum").html(percent + "%");
        $("#percentNum").css('width', percent + "%");
    }

    function getTime() {
        var ts = arguments[0] || 0;
        var t,y,m,d,h,i,s;
        t = ts ? new Date(ts*1000) : new Date();
        y = t.getFullYear();
        m = t.getMonth()+1;
        d = t.getDate();
        h = t.getHours();
        i = t.getMinutes();
        s = t.getSeconds();
        // 可依据须要在这里定义时间格式
        return y+'-'+(m<10?'0'+m:m)+'-'+(d<10?'0'+d:d)+' '+(h<10?'0'+h:h)+':'+(i<10?'0'+i:i)+':'+(s<10?'0'+s:s);
    }

    function update_info()
    {
        $.post("{{ URL('/contest/get_URG_info') }}", {cid:{{ $cid }}, _token:"{{ csrf_token() }}"},function (data) {
            if(data['status'] == true) {
                $("#info").html("");
                for(var i = 0; i < data.infos.length; ++i) {
                    $("#info").append('<p class="text-danger">'+data['infos'][i]['topic_question']+'</p><hr/>');
                }
            }
        })
    }

    $(document).ready(function(){
        update_info();
        setInterval(auto_event,1000);
        setInterval(update_info, 60000);
    })
</script>
@endsection