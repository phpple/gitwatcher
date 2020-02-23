<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 12:27 上午
 * @copyright: 2020@100tal.com
 * @filesource: WatcherLoader.php
 */

namespace Phpple\GitWatcher\Watcher;


class WatcherLoader
{
    const WATCHER_FORBIDDEN_MERGE = 'forbidden_merge';
    const GIT_VERSION = 'git_version';

    const WATCHER_LIST = [
        self::GIT_VERSION,
        self::WATCHER_FORBIDDEN_MERGE,
    ];

    /**
     * 载入loader
     * @param string $name
     * @return WatcherInterface
     * @throws \Exception
     */
    public static function initWatcher(string $name, array $conf)
    {
        $className = __NAMESPACE__ . "\\" . str_replace('_', '', ucwords($name, '_')) . 'Watcher';
        $watcher = new $className();
        if (!($watcher instanceof WatcherInterface)) {
            throw new \Exception('watcher must be instance of WatcherInterface');
        }

        $watcher->init($conf);
        return $watcher;
    }

    /**
     * pre-commit对应的处理
     * @return bool
     * @throws \Exception
     */
    public static function preCommit():bool
    {
        foreach (self::WATCHER_LIST as $name => $required) {
            chdir(SITE_ROOT);
            $watcher = self::initWatcher($name, []);
            if (!$watcher->check()) {
                return false;
            }
        }
        return true;
    }
}