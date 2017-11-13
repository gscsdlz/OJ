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
        <div class="carousel-inner" role="listbox">
            <div class="item active">
                <img src="{{ URL('image/1.jpg') }}" alt="...">
                <div class="carousel-caption">
                    <h1><a href="{{ URL('video/1.mp4') }}">Everybody should learn how to program</a></h1>
                </div>
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