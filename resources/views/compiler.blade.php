@extends('layout')
@section('title')
    编译错误信息
@endsection
@section('main')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-success">
            <div class="panel-heading">
                <h3 class="text-success">用户<a href="{{ URL("/user/show/".$userInfo->username) }}">{{ $userInfo->username }}({{ $userInfo->nickname }})</a>的提交记录  记录号：{{ $submitInfo->submit_id }}</h3>
                @if($cid != 0)
                <h4 class="text-muted">题目编号：<a href="{{ URL('contest/show/'.$cid.'/'.$submitInfo->pro_id) }}">{{ $submitInfo->pro_id }}</a></h4>
                @else
                <h4 class="text-muted">题目编号：<a href="{{ URL("/problem/show/".$submitInfo->pro_id) }}">{{ $submitInfo->pro_id }}</a></h4>
                @endif
                <h4 class="text-muted">提交时间：  {{ date('Y-m-d H:i:s', $submitInfo->submit_time) }}</h4>
                <h4 class="text-muted">运行时间： {{ $submitInfo->run_time }}MS 运行内存： {{ $submitInfo->run_memory }}KB</h4>
                {{--*/ $langArr = config('web.langArr') /*--}}
                {{--*/ $statusArr = config('web.statusArr') /*--}}
                <h4 class="text-danger">语言： {{ $langArr[$submitInfo->lang] }}</h4>
                <h4 class="text-danger">状态： {{ $statusArr[$submitInfo->status] }}</h4>
            </div>
            <div class="panel-body"><pre class="line-numbers command-line data-line"><code class="language-c" style="font-size: 18px;">{{ $ce->info }}</code></pre></div>
        </div>
    </div>
</div>
@endsection
