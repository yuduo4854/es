<?php
//创建WebSocket Server对象，监听0.0.0.0:9502端口
#更改端口号
$ws = new Swoole\WebSocket\Server('0.0.0.0', 9509);

$redis = new Redis();
$redis->connect('127.0.0.1','6379');
//监听WebSocket连接打开事件
$ws->on('Open', function ($ws, $request) {
    $ws->push($request->fd, json_encode("心跳已连接",true));
    // print_r('连接成功');
});
$ws->set([
    'heartbeat_idle_time'      => 60, // 表示一个连接如果60秒内未向服务器发送任何数据，此连接将被强制关闭
    'heartbeat_check_interval' => 60,  // 表示每60秒遍历一次
    'worker_num' => 4,         //设置启动的worker进程数。【默认值：CPU 核数】
    'max_request' => 10000,    //设置每个worker进程的最大任务数。【默认值：0 即不会退出进程】
    'daemonize' => 0,          //开启守护进程化【默认值：0】
]);

//监听WebSocket消息事件
$ws->on('Message', function ($ws, $frame) use ($redis){
    // print_r($frame);
    $data = json_decode($frame->data,true);
    // print_r($data);
    switch ($data['type']) {
        case 'open':
            // code...
            $redis->set($data['my'],$frame->fd);
            echo($data['my']);

        case 'send':
            // code...
            $to=$redis->get($data['to']);
            echo($to);
            // print_r($to);
            // echo($to);
            break;
            $msg = [
                'msg'=>$data['data'],
                'name'=>$data['my']
            ];

            print_r($msg);
            $ws->push($to,json_encode($msg,true));
            break;
    }
});

//监听WebSocket连接关闭事件
$ws->on('Close', function ($ws, $fd) {
    echo "client-{$fd} is closed\n";
});

$ws->start();