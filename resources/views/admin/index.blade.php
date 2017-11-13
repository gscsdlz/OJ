@extends('admin.layout')
@section('title')
    主页
@endsection
@section('main')
    <hr/>
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-danger">
            <div class="panel-heading">
                统计信息
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>题目总数</th>
                        <th id="v1">{{ $pros }}</th>
                    </tr>
                    <tr>
                        <th>总用户数</th>
                        <th>{{ $users }}</th>
                    </tr>
                    <tr>
                        <th>总提交次数</th>
                        <th>{{ $submits }}</th>
                    </tr>
                    <tr>
                        <th>比赛次数</th>
                        <th>{{ $contests }}</th>
                    </tr>
                    <tr>
                        <th>运行时间</th>
                        <th>{{ (int)((time() - 1458835200) / 60 / 60 / 24) }}天</th>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
