<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('css/prism.css') }}" rel="stylesheet">
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('js/prism.js') }}"></script>
    <script src="https://cdn.bootcss.com/echarts/3.7.2/echarts.min.js"></script>
    <script src="{{ URL::asset('js/AjaxFileUpload.js') }}"></script>
    <link rel="shortcut  icon" type="image/x-icon" href="{{ URL::asset('favicon.ico') }}" media="screen"  />
    <title>@yield('title')</title>
</head>

<body style="background-color:rgb(248,248,248)">	<!--禁止横向滑动-->
<hr/>
<hr/>
@if(isset($cid) && $cid != 0)
    @include('contest_navbar')
@else
    @include('navbar')
@endif
@yield('main')
﻿<nav class="navbar navbar-inverse" style="background:rgb(248,248,248); border:0px solid white" role="navigation">
    <div class="container">

        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">

                <h3>Welcome to NUC Online Judge</h3>
                <table class="table">
                    <tr>
                        <td colspan="3">中北大学ACM-ICPC程序设计创新实验室 版权所有</td>
                    </tr>
                    <tr>
                        <td colspan="3">NUC Online Judge Version 2017.2 || Developed & Design By <a href="mailto:lz842063523@foxmail.com">gscsdlz</a></td>

                    </tr>
                </table>
                <h4>执行时间：{{ sprintf("%0.3f", microtime(true) - LARAVEL_START) }} &nbsp;
                    服务器时间: {{ date('Y-m-d H:i:s', time()) }} @Server One
                </h4>
            </div>
        </div>

    </div>
</nav>
</body>
</html>


