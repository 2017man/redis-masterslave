<?php
require __DIR__ . "/../../vendor/autoload.php";
require_once "./Config.php";

use Manson\RedisMasterslave\RedisMs;

var_dump($config);
(new RedisMs($config))->exec('get', ['A']);
//$http = new Swoole\Http\Server("0.0.0.0", 9000);
//$http->on('request', function ($request, $response) {
//    $response->end("<h1>Hello Swoole. #" . rand(1000, 9999) . "</h1>");
//});
//$http->start();