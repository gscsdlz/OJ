<h1>中北大学Online Judge免密登录链接</h1>
<p>{{ $username }}你好：</p>
<p>你在{{ date('Y-m-d H:i:s') }}发起找回密码请求，请求细节如下：</p>
<p>浏览器信息：{{ $ua }}</p>
<p>IP：{{ $ip }}</p>
<p><a href="{{ $url }}">点击我</a>或者复制 {{ $url }} 到浏览器中打开即可完成免密登录</p>
<p style="color:red">如果不是本人操作请勿点击链接</p>
