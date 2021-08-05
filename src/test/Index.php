<?php
require __DIR__ . "/../../vendor/autoload.php";
require_once "./Config.php";

use Manson\RedisMasterslave\RedisMs;
use Manson\RedisMasterslave\Input;

// 在swoole事件中 echo 和 var_dump是输出在 控制台 不是浏览器
$http = new Swoole\Http\Server("0.0.0.0", 9501);

// 设置swoole进程个数
$http->set([
    'worker_num' => 1
]);
// 在创建的时候执行  ； 进程创建的时候触发时候
// 理解为一个构造函数，初始化
$http->on('workerStart', function ($server, $worker_id) use ($config) {
    global $redisMS;
    $redisMS = new RedisMs($config);
});

// 通过浏览器访问 http://本机ip ：9501会执行的代码
$http->on('request', function ($request, $response) {
    global $redisMS;
    $params  = $request->get['params'];
    $command = '';
    if ($request->get['method']) {
        $command = $request->get['method'];
    }
    if ($params) {
        $params = explode(',', $params);
    }
    Input::info(['commond' => $command, 'parames' => $params], '请求参数');
    $ret = $redisMS->exec($command, $params);
    $response->end($ret);
});

$http->start();