<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 8:03 下午
 * @copyright: 2020@100tal.com
 * @filesource: ConsoleUtil.php.php
 */

namespace Phpple\GitWatcher\Foundation\Util;


class ConsoleUtil
{

    /**
     * 读取标准输入
     * @return string
     */
    public static function stdin(): string
    {
        return fgets(STDIN);
    }

    /**
     * 标准输出
     * @param string $msg
     */
    public static function stdout($msg)
    {
        if (!is_scalar($msg)) {
            $msg = var_export($msg, true);
        }
        fwrite(STDOUT, $msg.PHP_EOL);
    }

    public static function stderr($msg)
    {
        if (is_array($msg)) {
            $msg = implode(PHP_EOL, $msg);
        }
        if (!is_scalar($msg)) {
            $msg = var_export($msg, true);
        }
        fwrite(STDERR, $msg.PHP_EOL);
    }
}