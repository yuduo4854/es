<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>


/**
* wxml样式
*/
        <view class="li-send" bindtap="onSend" wx:if="{{send}}"><button>发送验证码</button></view>
        <view class="li-send"wx:else><button>{{seconds}}s后发送</button></view>

/**
* js操作
*/

        data: {
        // 发送按钮显示
        send:true,
        // 当前倒计时秒数
        seconds:"",
        // 总秒数
        max_seconds:5,
        },

        onSend(){
        var that=this;
        // 获取总秒数
        var seconds=this.data.max_seconds;
        this.setData({
        // 显示倒计时
        send:false,
        // 设置秒数
        seconds:seconds,
        })
        // 设置定时器
        var t=setInterval(function(){
        // 如果秒数小于0
        if(seconds<=0){
        // 停止定时器
        clearInterval(t);
        that.setData({
        // 显示发送按钮
        send:true,
        })
        // 停止执行
        return;
        }
        // 秒数减一
        seconds--;
        that.setData({
        // 更新当前秒数
        seconds:seconds,
        })
        },1000)
        },








</body>
</html>



