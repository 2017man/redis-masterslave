<?php

namespace Manson\RedisMasterslave;


class RedisMs
{
    protected $config;
    protected $connections;
    /**
     * 从节点标识数组
     */
    protected $connSlaveIndexs;
    /**
     * 可接受的主从最大偏移量
     */
    const MAX_OFFSET = 100;

    public function __construct($config)
    {
        $this->config = $config;
        $this->createMaster($config['master']);
        $this->createSlaves($config['slaves']);
        $this->maintain();
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
        foreach ($slaves as $slave) {
            $this->connections['slaves'][$this->serverFlag($slave)] = $this->getRedis($slave['host'], $slave['port']);
        }
        // 所有从节点的下标标识key
        $this->connSlaveIndexs = array_keys($this->connections['slaves']);
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

    /**
     * 主从维护
     * 动态新增剔除从节点
     * 提高延迟行
     */
    public function maintain()
    {
        /**
         * info replication主从信息
         * role:master
         * connected_slaves:2
         * slave0:ip=192.160.1.140,port=6379,state=online,offset=42168,lag=1
         * slave1:ip=192.160.1.130,port=6379,state=online,offset=42168,lag=1
         * master_replid:7410eff41dbae065a44d42aa1da013ffcc90d724
         * master_replid2:0000000000000000000000000000000000000000
         * master_repl_offset:42168
         * second_repl_offset:-1
         * repl_backlog_active:1
         * repl_backlog_size:1048576
         * repl_backlog_first_byte_offset:1
         * repl_backlog_histlen:42168
         * 1.获取主节点信息
         * 2.获取所有从节点
         *  2.1 获取从节点offset
         *  2.2 根据偏移量
         *  2.3 动态增加
         */
        $masterRedis = $this->connections['master'];
        $masterRept  = $masterRedis->info('replication');
        $slaves      = [];
        for ($i = 0; $i < $masterRept['connected_slaves']; $i++) {
            $slaveInfo  = $this->strToArr($masterRept['slave' . $i]);
            $slave      = ['host' => $slaveInfo['ip'], 'port' => $slaveInfo['port']];
            $serverFlag = $this->serverFlag($slave);

            // 动态新增延迟范围内的从节点
            if ($masterRept['master_repl_offset'] - $slaveInfo['offset'] < self::MAX_OFFSET) {
                // 是正常范围
                // 如果之前因为网络延迟删除了节点，现在恢复了网络 -》新增
                if (!in_array($serverFlag, $this->connSlaveIndexs)) {
                    $slaves[] = $slave;
                }
                Input::info($slaves, '新增从节点');
            } else {
                // 动态剔除延迟搞的节点
                if (isset($this->connections['slaves'][$serverFlag])) {
                    unset($this->connections['slaves'][$serverFlag]);
                    Input::info($serverFlag, '剔除从节点');
                }
            }
        }
        $this->connections['slaves'] = $this->createSlaves($slaves);
    }

    protected function strToArr($str = '', $flag1 = ',', $flag2 = '=')
    {
        /**
         * ip=192.160.1.140,port=6379,state=online,offset=42168,lag=1
         */
        $ret = [];
        foreach (explode($flag1, $str) as $item) {
            foreach ($item as $value) {
                $value          = explode($flag2, $value);
                $ret[$value[0]] = $value[1];
            }
        }
        return $ret;

    }

}