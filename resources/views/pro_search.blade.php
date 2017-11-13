@extends('layout')
@section('title')
    题号、标题、来源搜索
@endsection
@section('main')
<div class="row">
    <div class="col-md-6 col-md-offset-3 text-center">
        <table class="table table-hover text-left">
            <tr>
                <th>题目编号</th>
                <th>题目名</th>
                <th>来源</th>
            </tr>
            @foreach($pros as $row)
                <tr>
                    <td>{{ $row->pro_id }}</td>
                    <td align="left"><a href="{{ URL('problem/show/'.$row->pro_id) }}">{{ $row->pro_title }}</a></td>
                    <td>{{ $row->author }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection

