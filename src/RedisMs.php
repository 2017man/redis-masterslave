<?php

namespace Manson\RedisMasterslave;


class RedisMs
{
    protected $config;
    protected $connections;

    public function __construct($config)
    {
        $this->config = $config;
        $this->createMaster($config['master']);
        $this->createSlaves($config['slaves']);
    }

    /**
     * redis连接信息
     * @param $host
     * @param $port
     * @param string $password
     * @return \Redis
     */
    public function getRedis($host, $port, $password = '')
    {
        $redis = new \Redis();
        $redis->pconnect($host, $port);
        if ($password) {
            $redis->auth($password);
        }
        return $redis;
    }

    /**
     * 主服务连接节点信息
     * @param $master
     */
    public function createMaster($master)
    {
        $this->connections['master'] = $this->getRedis($master['host'], $master['port']);
    }

    /**
     * 从服务节点连接信息
     * @param $slaves
     */
    public function createSlaves($slaves)
    {
        foreach ($slaves as $key => $slave) {
            $this->connections['slaves'][$this->serverFlag($slave)] = $this->getRedis($slave['host'], $slave['port']);
        }
    }

    /**
     * 服务标识
     * @param $slave
     * @return string
     */
    protected function serverFlag($slave)
    {
        return $slave['host'] . ":" . $slave['port'];
    }
}