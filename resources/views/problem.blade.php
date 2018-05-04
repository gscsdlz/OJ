@extends('layout')
@section('title')
    {{ $pro->pro_title }}
@endsection
@section('main')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h1 class="text-center text-primary">{{ $pro->pro_title }}</h1>
        <h4 class="text-center text-danger">时间限制: <span class="badge">C/C++ {{ $pro->time_limit }}ms; Java {{ $pro->time_limit * 2 }}ms</span> 内存限制: <span class="badge">{{ $pro->memory_limit }}KB</span></h4>
        <h4 class="text-center text-danger">通过次数: <span class="badge">{{ $pro->getACNum() }}</span> 总提交次数: <span class="badge">{{ $pro->getAllNum() }}</span></h4>
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
                        <button data-toggle="modal" id="getStatistics" data-target="#statisticsModal" type="button" class="btn btn-success btn-lg">统计</button>
                        <button type="button" class="btn btn-info btn-lg">讨论</button>
                        <div class="well" id="AllStatus" style="height: 400px; margin-top: 50px;">

                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                        <input type="text" readonly="readonly" class="form-control" id="pid" value="{{ $pro->pro_id }}">
                        <select class="form-control" id="lang">
                            {{--*/ $langArr = config('web.langArr') /*--}}
                            @for($i = 0; $i < count($langArr); $i++)
                                @if(Session::has('lang') && Session::get('lang') == $i)
                                    <option value="{{ $i }}" selected>{{ $langArr[$i] }}</option>
                                @else
                                    <option value="{{ $i }}">{{ $langArr[$i] }}</option>
                                @endif
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
            $.post("{{ URL('submit') }}", {pro_id:pid, lang:lang, codes:codes,_token:"{{ csrf_token() }}"
        }, function(data){
                if(data.status == true)
                        location.href="{{ URL('/status') }}"
                else
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
    var  myChartAllStatus = echarts.init(document.getElementById('AllStatus'));
    $(document).ready(function(){
        $("#AllStatus").hide();
        $("#getStatistics").click(function(){
            $("#AllStatus").toggle();
            var arr = new Array(0, 0, 0,0,0,0,0, 0);
            $.post("{{ URL('problem/get_statistics') }}", {
                pro_id:{{ $pro->pro_id }},
                _token:"{{ csrf_token() }}"
            }, function(data){
                var arrs = eval("(" + data + ")");
                    for(var i = 0; i < arrs.length; ++i) {
                        arr[parseInt(arrs[i]['status']) - 4] = parseInt(arrs[i]['count(*)']);
                    }
                myChartAllStatus.setOption({
                    title: {
                        text:'提交统计信息'
                    },
                    color: ['#2F4554'],
                    tooltip : {
                        trigger: 'axis',
                        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                        }
                    },
                    grid: {
                        left: 'center',
                        top: 'middle',
                        height: '300',
                        containLabel: true
                    },
                    xAxis : [
                        {
                            type : 'category',
                            data : ['答案正确', '格式错误', '答案错误', '运行时错误', '超时', '超内存', '超输出', '编译错误'],
                            axisTick: {
                                alignWithLabel: true
                            }
                        }
                    ],
                    yAxis : [
                        {
                            type : 'value'
                        }
                    ],
                    series : [
                        {
                            name:'详细数据',
                            type:'bar',
                            barWidth: '60%',
                            data:arr
                        }
                    ]
                });
            })
        })
    })
</script>
@endsection