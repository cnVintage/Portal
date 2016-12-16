Portal
======

This is a simple util that will generate a token that can be used to log-in in some non-secure sub sites (e.g. telnet, legacy site). 

TODO: 

### HTML Example
```html
<h1>Telnet 论坛</h1>
<p>
  本站现已支持使用Telnet访问！网址同主站网址，为 www.cnvintage.org，端口为 50000。
  当前您的访问Token：<span id="telnet-token">未刷新</span>
</p>
<button type="submit" class="Button" onclick="flushToken()">
  <span class="Button-label">刷新Token</span>
</button>
<script>
function flushToken() {
  // New Ajax request.
  var xhr = new XMLHttpRequest();

  // Open it.
  xhr.open('GET', '/portal.php');

  // Setup event listener.
  xhr.onreadystatechange = function() {
    switch(xhr.readyState) {
      case XMLHttpRequest.DONE: if (xhr.status === 200) {
        var res = JSON.parse(xhr.responseText);
        if (res.status === 'success') {
          document.querySelector('#telnet-token').innerText = res.token;
        } else {
          document.querySelector('#telnet-token').innerText = '出现错误：' + res.message;
        }
      }
      break;
      case XMLHttpRequest.LOADING:
        document.querySelector('#telnet-token').innerText = '正在获取...';
      default: break;
    }
  };

  // Let it go.
  xhr.send();
}
</script>
```

### Database
```sql
CREATE TABLE `fl_telnet_access_tokens` (
  `remote_addr` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_token` varchar(10) NOT NULL,
  `flarum_token` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
