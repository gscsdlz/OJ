<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ URL::asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <script src="{{ URL::asset('js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('js/bootstrap.min.js') }}"></script>
    <link rel="shortcut  icon" type="image/x-icon" href="{{ URL::asset('favicon.ico') }}" media="screen"  />
    <title>颁奖仪式</title>
    <style>
        .border-shadow span{
            border: 1px solid rgb(240,240,240);
            border-radius: 10px;
            -moz-box-shadow:10px 10px 20px gray;
            -webkit-box-shadow:10px 10px 20px gray;
            box-shadow:10px 10px 20px gray;
            background-color: white;
        }
        span{
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        .back {
            background-image: url({{ URL::asset('image/back.jpg') }});
            background-position:  0px 0px;
            background-repeat: no-repeat;
            background-size:contain;
            width:100%;
        }
    </style>
</head>

<body style="background-color:rgb(248,248,248)">	<!--禁止横向滑动-->
<div class="container-fluid back">
    <div class="row">
        <div class="col-md-12 text-center border-shadow" id="back">
            <h3>第三届山西省大学生程序设计大赛</h3>
            <h4 class="text-right">颁奖仪式&nbsp;&nbsp;&nbsp;&nbsp;</h4>
            <hr/>
            <p class="text-left">您的唯一编号</p>
            <span style="padding: 0px 20px;font-size: 40px;font-weight: bold">{{ substr($info, 0, 1) }}</span>
            <span style="padding: 0px 20px;font-size: 40px;font-weight: bold">{{ substr($info, 1, 1) }}</span>
            <span style="padding: 0px 20px;font-size: 40px;font-weight: bold">{{ substr($info, 2, 1) }}</span>
            <span style="padding: 0px 20px;font-size: 40px;font-weight: bold">{{ substr($info, 3, 1) }}</span>
            <hr/>
            <p class="text-right">该编号将用于抽奖和兑换奖品，请妥善保管</p>
            <p class="text-right text-danger"><b>请截屏或者将该微信消息置顶</b></p>
        </div>
    </div>
</div>
<hr/>
<p class="text-center">中北大学ACM程序设计创新实验室 {{ date('Y-m-d') }}</p>
<script>
    $(document).ready(function () {
        var t = window.innerWidth||document.body.clientWidth||document.documentElement.clientWidth;
        $("#back").css('height', t / 1080 * 1920 );
    })
</script>
</body>
</html>


