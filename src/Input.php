<?php

namespace Manson\RedisMasterslave;

/**
 * 格式化输出信息
 */
class Input
{
    public static function info($message, $description = null)
    {
        echo "======>>> " . $description . " start\n";
        if (\is_array($message)) {
            echo \var_export($message, true);
        } else if (\is_string($message)) {
            echo $message . "\n";
        } else {
            var_dump($message);
        }
        echo "======>>> " . $description . " end\n";
    }
}
