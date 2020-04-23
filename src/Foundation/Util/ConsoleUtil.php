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
    const FOREGROUND_COLORS = [
        'black' => '0;30',
        'dark_gray' => '1;30',
        'blue' => '0;34',
        'light_blue' => '1;34',
        'green' => '0;32',
        'light_green' => '1;32',
        'cyan' => '0;36',
        'light_cyan' => '1;36',
        'red' => '0;31',
        'light_red' => '1;31',
        'purple' => '0;35',
        'light_purple' => '1;35',
        'brown' => '0;33',
        'yellow' => '1;33',
        'light_gray' => '0;37',
        'white' => '1;37',
    ];
    const BACKGROUND_COLORS = [
        'black' => '40',
        'red' => '41',
        'green' => '42',
        'yellow' => '43',
        'blue' => '44',
        'magenta' => '45',
        'cyan' => '46',
        'light_gray' => '47',
    ];

    /**
     * Get text with color
     * @param string $string black|dark_gray|blue|light_blue|green|light_green|cyan|light_cyan|red|light_red|purple|brown|yellow|light_gray|white
     * @param string|null $foregroundColor foreground color black|red|green|yellow|blue|magenta|cyan|light_gray
     * @param string|null $backgroundColor background color, the same as $foregroundColor
     * @return string
     */
    public static function getColoredString(
        string $string,
        $foregroundColor = null,
        $backgroundColor = null
    ) {
        $coloredString = '';

        if (isset(static::FOREGROUND_COLORS[$foregroundColor])) {
            $coloredString .= "\033[" . static::FOREGROUND_COLORS[$foregroundColor] . "m";
        }
        if (isset(static::BACKGROUND_COLORS[$backgroundColor])) {
            $coloredString .= "\033[" . static::BACKGROUND_COLORS[$backgroundColor] . "m";
        }

        $coloredString .= $string . "\033[0m";

        return $coloredString;
    }

    /**
     * Output normal information
     * @param $msg
     */
    public static function notice(string $msg)
    {
        fwrite(STDOUT, self::getColoredString($msg, 'light_gray') . PHP_EOL);
    }

    /**
     * Output error information
     * @param $msg
     */
    public static function error(string $msg)
    {
        fwrite(STDERR, self::getColoredString($msg, 'red') . PHP_EOL);
    }

    /**
     * Output warning information
     * @param $msg
     */
    public static function warn(string $msg)
    {
        fwrite(STDOUT, self::getColoredString($msg, 'yellow') . PHP_EOL);
    }

    /**
     * Ouput success information
     * @param $msg
     */
    public static function success(string $msg)
    {
        fwrite(STDOUT, self::getColoredString($msg, 'green') . PHP_EOL);
    }

    /**
     * Get the standard input
     * @return string
     */
    public static function stdin(): string
    {
        return rtrim(fgets(STDIN), PHP_EOL);
    }

    /**
     * Standard output
     * @param string $msg
     */
    public static function stdout($msg)
    {
        if (is_array($msg)) {
            $msg = implode(PHP_EOL, $msg);
        }
        if (!is_scalar($msg)) {
            $msg = var_export($msg, true);
        }
        fwrite(STDOUT, $msg . PHP_EOL);
    }

    /**
     * Standard error output
     * @param $msg
     */
    public static function stderr($msg)
    {
        if (is_array($msg)) {
            $msg = implode(PHP_EOL, $msg);
        }
        if (!is_scalar($msg)) {
            $msg = var_export($msg, true);
        }
        fwrite(STDERR, $msg . PHP_EOL);
    }
}
