<?php

////创建webSocket Server对象，监听0.0.0.0：9501端口号
//$ws = new Swoole\WebSocket\Server('0.0.0.0', 9507);
////监听WebSocket连接打开事件
//$ws->on('Open', function ($ws, $request) {
//    echo 1;
//    //向小程序端进行一个连接返回，告知用户已经连接上服务器，返回值需要转为json数据格式
//    $ws->push($request->fd, json_encode(['msg'=>'用户已连接'],true));
//});
//// 心跳监听
////$server->set([
////    'heartbeat_idle_time'      => 600,
////    'heartbeat_check_interval' => 60,
////    'daemonize' => true
////]);
//$redis = new Redis();
//$redis->connect('127.0.0.1','6379');
//
////监听WebSocket消息事件
//$ws->on('Message', function ($ws, $frame) use ($redis) {
//    //将我们从小程序端传递过来的数据转换成数组格式进行处理
//    $data = json_decode($frame->data,true);
//
//    //通过小程序端传递过来的类型进行处理
//    switch($data['type']){
//
//        //如果类型为连接
//        case "open" :
//            //将登录的用户昵称设为键，保存用户id
//            $redis->set($data['my'],$frame->fd);
//            break;
//        //如果类型为发送
//        case "send" :
//            //取出我们发送给对方的id
//            //【当前登陆用户为yuchen，我们要发送给miao，存进去的用户信息为yuchen信息，取出来的是miao信息】
//            //【当前登陆用户为miao，我们要发送给yuchen，存进去的用户信息为miao信息，取出来的是yuchen信息】
//            $toId = $redis->get($data['to']);
//            //将用户昵称和所要发送的消息返回给小程序端
//            $msg = [
//                'user' => $data['my'],
//                'msg' => $data['data']
//            ];
//            //我们要将消息push给对方，也就是当前登录yuchen，push给miao
//            return $ws->push($toId,json_encode($msg));
//            break;
//        default:
//
//            break;
//    }
//
//});
//
////监听WebSocket连接关闭事件
//$ws->on('Close', function ($ws, $fd) {
//    echo "client-{$fd} is closed\n";
//});
//
//$ws->start();

$ws = new Swoole\WebSocket\Server('0.0.0.0', 9507);

//监听WebSocket连接打开事件
$ws->on('Open', function ($ws, $request) {
    //向小程序端进行一个连接返回，告知用户已经连接上服务器，返回值需要转为json数据格式
    $ws->push($request->fd, json_encode(['msg' => '用户已连接'], true));
});
// 心跳监听
//$server->set([
//    'heartbeat_idle_time' => 600,
//    'heartbeat_check_interval' => 60,
//    'daemonize' => true
//]);
//监听WebSocket消息事件
$redis = new Redis();
$redis->connect('127.0.0.1','6379');
$ws->on('Message', function ($ws, $frame) use ($redis) {
    //将我们从小程序端传递过来的数据转换成数组格式进行处理
    $data = json_decode($frame->data, true);

    //通过小程序端传递过来的类型进行处理
    switch ($data['type']) {

        //如果类型为连接
        case "open" :
            //将登录的用户昵称设为键，保存用户id
            $redis->set($data['my'], $frame->fd);
            break;
        //如果类型为发送
        case "send" :
            //取出我们发送给对方的id
            //【当前登陆用户为yuchen，我们要发送给miao，存进去的用户信息为yuchen信息，取出来的是miao信息】
            //【当前登陆用户为miao，我们要发送给yuchen，存进去的用户信息为miao信息，取出来的是yuchen信息】
            $toId = $redis->get($data['to']);
            //将用户昵称和所要发送的消息返回给小程序端
            $msg = [
                'user' => $data['my'],
                'msg' => $data['data']
            ];
            //我们要将消息push给对方，也就是当前登录yuchen，push给miao
            return $ws->push($toId, json_encode($msg));
            break;
        default:
            break;
    }
});

//监听WebSocket连接关闭事件
$ws->on('Close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();

