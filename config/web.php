<?php
/**
 * 该文件控制网站所有的相关配置选项
 */
return [

    'langArr' => [
        '请选择语言','C','C++','Java'
    ],
    /*由于这里的配置和数据库并未进行同步处理，所以添加了的语言，进行删除的时候，
        请自行处理好数据库status表中有关该语言的提交，否则会出现异常*/
    'statusArr' => [
        'All', 					//0
        'Queuing',				//1
        'Compiling',			//2
        'Running',				//3
        'Accepted',				//4
        'Presentation Error',	//5
        'Wrong Answer',        	//6
        'Runtime Error',		//7
        'Time Limit Exceeded',	//8
        'Memory Limit Exceeded',//9
        'Output Limit Exceeded',//10
        'Compilation Error',	//11
        'Rejudgeing',			//12
        'Cheating'				//13
    ],
    'statusInfo' => [
        '',
        '等待判题中',
        '编译中',
        '运行中',
        '答案正确',
        '格式错误',
        '答案错误',
        '运行时错误',
        '超时',
        '超内存',
        '超输出',
        '编译错误',
        '重判',
        '作弊代码',
    ],
    'RankPageMax' => '50',
    'ProblemPageMax' => '100',
    'StatusPageMax' =>' 20',
    'vcode' => false,
    //QQ互联配置信息
    'QQ_APPID' => env('QQ_APPID', ''),
    'QQ_REDIRECT_URL' => env('QQ_REDIRECT_URL', ''),
    'QQ_APPKEY' => env('QQ_APPKEY', ''),

];