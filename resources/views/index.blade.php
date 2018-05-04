@extends('layout')
@section('title')
    Welcome To NUC Online Judge
@endsection
@section('main')

    <div style="margin-top:-80px" id="carousel" class="carousel slide" data-ride="carousel" data-pause="false"  data-interval="60000">
        <!-- Indicators -->
        <ol class="carousel-indicators">
            <li data-target="#carousel" data-slide-to="0" class="active"></li>
            <li data-target="#carousel" data-slide-to="1"></li>
            <li data-target="#carousel" data-slide-to="2"></li>
        </ol>
        <script>
            /*$(document).ready(function(){
                var height = window.screen.height;
                var width = window.screen.width;
                $("#background").css('height', height);
                $("#background").css('width', width);
            })*/
        </script>
        <!-- Wrapper for slides -->
        <marquee width="40%" scrollamount="5"height="40"  style="position:absolute;z-index:1000;left:30%; top:45%;"><a href="joinus2018" style="color:red;text-decoration:none;font-size:35px; font-family:隶书;">山西省第三届大学生程序设计大赛点击报名</a></marquee>
        <div class="carousel-inner" role="listbox">
            <div class="item active">
				<a href="joinus2018">
                <img src="{{ URL('image/index.jpg') }}" alt="...">
                <div class="carousel-caption">
                    <!--<h1><a href="{{ URL('video/1.mp4') }}">Everybody should learn how to program</a> <h1><a href="joinus2018">山西省第三届大学生程序设计大赛报名入口--报名不收费</a></h1></h1>-->		
			<h1><a href="http://acm.nuc.edu.cn/OJ/joinus2018/deal_form.html">山西省第三届大学生程序设计大赛-中北大学校外参赛人员现场赛确认-</a></h1>
                </div>
				</a>
            </div>
            <div class="item">
                <img src="{{ URL('image/2.jpg') }}" alt="...">
                <div class="carousel-caption">
                    <h1>了解什么是ACM-ICPC吧！</h1>
                </div>
            </div>
            <div class="item">
                <img src="{{ URL('image/3.jpg') }}" alt="...">
                <div class="carousel-caption">
                    <h1>快来加入ACM-Team吧！</h1>
                </div>
            </div>
        </div>
    </div>
@endsection