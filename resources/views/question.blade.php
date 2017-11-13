@extends('layout')
@section('title')
    问答
@endsection
@section('main')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="row">
                <div class="col-md-6">
                    <ol class="text-danger">
                        <li>提问请言简意赅。</li>
                        <li>提问或者回答问题，不可以直接使用代码，也不可以给出直接或间接的暗示。</li>
                        <li>所有提问和回答，只能由管理员删除。</li>
                        <li>请大家文明比赛，诚信比赛</li>
                    </ol>
                </div>
                <div class="col-md-6 text-right">
                    <form class="form-horizontal well">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">题目</label>
                            <div class="col-sm-6">
                                <select class="form-control" id="pro_id">
                                    <option value="0">All</option>
                                    @foreach($problems as $pro)
                                        <option value="{{ $pro->inner_id }}">{{ $pro->inner_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group text-left">
                            <label class="col-sm-2 control-label">问题</label>
                            <div class="col-sm-10">
                                <input type="text" value="" id="question" placeholder="请输入问题详情" class="form-control" />
                                <label class="text-danger" id="questionEmptyError">问题不能为空或字数超过限制</label>
                            </div>

                        </div>
                    </form>
                    @if(Session::has('user_id'))
                        <button class="btn btn-primary" type="button" id="submit">发布新问题</button>
                    @endif
</div>
</div>

<hr/>
@foreach($lists as $q)
<div class="panel panel-default">
<div class="panel-heading">
    <ol class="breadcrumb">
        <li><a href="#">{{ $q->username }}</a></li>
        <li><a href="#">{{ date('Y-m-d H:i:s', $q->ask_time) }}</a></li>
        @if(is_null($q->pro_id) || $q->pro_id <= 0)
            <li><a href="#">All</a></li>
        @else
            <li><a href="{{ URL('contest/show/'.$cid.'/'.$q->pro_id) }}">{{ $q->pro_id }} </a></li>
        @endif
        <li class="active">{{ $q->topic_question }}</li>
        <img class="show" style="float: right;cursor: pointer" src="{{ URL('image/show.png') }}" width="20px"/>
        <input type="hidden" value="{{ $q->question_id }}"/>
    </ol>
</div>
<div class="panel-body">
</div>
@if(Session::has('user_id'))
<div class="panel-footer text-right">
    <form class="form-horizontal">
        <div class="form-group">
            <div class="col-sm-10">
                <input type="text" class="form-control" value=""  placeholder="请输入相关内容并回复！">
            </div>
            <div class="col-sm-2">
                <button class="btn btn-primary" type="button" onclick="do_reply($(this))">添加回复</button>
            </div>
        </div>
    </form>
</div>
@endif
</div>
@endforeach
</div>
</div>
<script>
var token = "{{ csrf_token() }}";
    $(document).ready(function () {
        $("#questionEmptyError").hide();
        $(".panel-body").hide();
        $(".panel-footer").hide();

        $(".show").click(function () {
        $(".panel-body").hide();
        $(".panel-footer").hide();
        $(this).parent().parent().next().fadeIn();
        $(this).parent().parent().next().next().fadeIn();

        var qid = $(this).next().val();
        var target = $(this).parent().parent().next();

        $.post("{{ URL('/contest/get_answer') }}", {cid:{{ $cid }}, qid:qid, _token:token}, function (data) {
            target.html("");
            var str = '<ul class="list-group">'
            for(var  i = 0; i < data['res'].length; ++i) {
                str += '<li class="list-group-item"><ol class="breadcrumb"><li><a href="#"><img src="{{ URL('image/header') }}/'+data['res'][i]['headerpath']+'" width="20px" /></a></li> <li><a href="#">'+data['res'][i]['username']+'</a></li> <li class="active">'+data['res'][i]['reply_time']+'</li></ol>'+data['res'][i]['topic_answer']+'</li>'
            }
            str += '</ul>';
            target.append(str);
        })
    })
        @if(Session::has('user_id'))
        $("#submit").click(function(){

        $("#questionEmptyError").hide();

        var str = $("#question").val();
        if(str.length == 0 || str.length > 60) {
            $("#questionEmptyError").show();
        } else {
            var pro_id = $("#pro_id").val();
            $.post("{{ URL('contest/add_question') }}", {cid:{{ $cid }}, str:str, _token:token, pro_id:pro_id}, function(data){
                 window.location.reload()
            })
        }
        })
        @endif
    })

    function do_reply(target)
    {
        var str = $(target).parent().prev().children().eq(0).val();
        var qid = $(target).parent().parent().parent().parent().prev().prev().children().eq(0).children().eq(5).val();
        if(str.length != 0) {
            $.post("{{ URL('contest/add_answer') }}", {str:str, _token:token, question_id:qid}, function(data){
                 window.location.reload()
            })
        }
    }
</script>
@endsection