<!--<!DOCTYPE html>
<html>
<head>
    <title>404</title>



    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 96px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title" id="title">404</div>
    </div>
</div>
</body>
<script src="{{ URL::asset('js/jquery.min.js') }}"></script>
<script>
    $(document).ready(function(){
        window.setInterval('window.history.go(-1)', 3000);
        $("#title").onmousemove(function () {
            $(this).css('')
        })
    })
</script>
</html>
-->
@extends('layout')
@section('title')
    Four Zero Four
@endsection
@section('main')
    <div class="row" style="height: 400px;margin-top: 190px">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-danger">
                <div class="panel-heading text-center">
                    <h1>@if(isset($info)){{ $info }}@else这个页面发生404错误了！@endif</h1>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            window.setTimeout('window.history.go(-1)',3000);
        })
    </script>
@endsection