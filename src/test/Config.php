<?php
/**
 * 主存配置
 */
$config = [
    'is_ms'  => true,
    'master' => [
        'host' => '192.160.1.150',
        'port' => 6379,
    ],
    'slaves' => [
        'slave140' => [
            'host' => '192.160.1.140',
            'port' => 6379,
        ],
        'slave130' => [
            'host' => '192.160.1.130',
            'port' => 6379,
        ],
    ],
];