@extends('layout')
@section('title')
    关于我们
@endsection
@section('main')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    实验室简介
                </div>
                <div class="panel-body">
                    实验室成立于2005年9月，是我校大学生进行计算机程序设计、算法研究和实践的重要平台，同时也是我校大学生参加“ACM国际大学生程序设计”竞赛的选拔和集训基地。
                    自实验室创立以来，得到了学校各级领导的关怀，实验室积极招收队员进行程序设计和算法学习的训练，涌现出了一批优秀的队员，并多次参加了亚洲区中国赛区的现场比赛。
                    实验室每年开设大学生程序设计竞赛OJ平台、大学生程序设计竞赛OJ平台实验、ACM程序设计算法理论、ACM程序设计算法实验等选修课。
                </div>
            </div>
            <div class="panel panel-danger">
                <div class="panel-heading">
                    中北大学 ACM-TEAM 2017
                </div>
                <div class="panel-body text-center">
                    <img src="{{ URL('image/2017.jpg') }}" height="400px"/>
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    获奖情况
                </div>
                <div class="panel-body">

                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    队员去向
                </div>
                <div class="panel-body">

                </div>
            </div>
            <div class="panel panel-success">
                <div class="panel panel-heading">
                    OJ系统简介
                </div>
                <div class="panel panel-body">
                    <ul>
                        <li>系统基于CentOS 7</li>
                        <li>前端基于bootstrap+Blade+jQuery</li>
                        <li>判题核心基于<a href="https://github.com/zhblue/hustoj/">hustOJ</a></li>
                        <li>后端基于Laravel</li>

                        <li>感谢<a href="http://echarts.baidu.com">echarts</a>提供图表组件</li>
                        <li>感谢<a href="http://ueditor.baidu.com">ueditor</a>提供编辑器组件</li>
                        <li>感谢<a href="http://prismjs.com">prism</a>提供代码高亮显示组件</li>
                        <li>感谢<a href="http://github.com/davgothic/AjaxFileUpload">davgothic</a>提供AJAX文件上传组件</li>
                        <li>发布时间：2017年11月</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection