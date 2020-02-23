<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 12:27 上午
 * @copyright: 2020@100tal.com
 * @filesource: HookHandler.phpp
 */

namespace Phpple\GitWatcher;


use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;
use Phpple\GitWatcher\Watcher\WatcherInterface;

class HookHandler
{
    const FORBIDDEN_MERGE = 'forbidden_merge';
    const GIT_VERSION = 'git_version';
    const PHP_SYNTAX = 'php_syntax';
    const STANDARD = 'standard';

    const WATCHER_LIST = [
        self::GIT_VERSION => [
            'version' => '2.0.0'
        ],
        self::PHP_SYNTAX => [],
        self::FORBIDDEN_MERGE => [],
        self::STANDARD => [
            'standard' => 'PSR2',
        ],
    ];

    /**
     * 载入loader
     * @param string $name
     * @return WatcherInterface
     * @throws \Exception
     */
    public static function initWatcher(string $name, array $conf)
    {
        $className = __NAMESPACE__ . "\\Watcher\\" . str_replace('_', '', ucwords($name, '_')) . 'Watcher';
        $watcher = new $className();
        if (!($watcher instanceof WatcherInterface)) {
            throw new \Exception('watcher must be instance of WatcherInterface');
        }

        $watcher->init($conf);
        return $watcher;
    }

    /**
     * pre-commit对应的处理
     * @param string $dir
     * @return bool
     * @throws \Exception
     */
    public static function preCommit(string $dir): bool
    {
        foreach (self::WATCHER_LIST as $name => $conf) {
            chdir($dir);
            $watcher = self::initWatcher($name, $conf);
            ConsoleUtil::stdout('-------watcher:' . $name . ' start ------');
            $ret = $watcher->check();
            ConsoleUtil::stdout('-------watcher:' . $name . ' end ------');
            if ($ret === false) {
                return false;
            }
        }
        return true;
    }
}