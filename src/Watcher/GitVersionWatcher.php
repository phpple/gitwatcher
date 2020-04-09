<?php
/**
 * Watcher for checking the version of git
 * @author: ronnie
 * @since: 2020/2/23 12:35 am
 * @copyright: 2020@100tal.com
 * @filesource: GitVersionWatcher.php
 */

namespace Phpple\GitWatcher\Watcher;

use Phpple\GitWatcher\HookHandler;
use Phpple\GitWatcher\WatcherInterface;

class GitVersionWatcher implements WatcherInterface
{
    private $minVersion;
    const DEFAULT_MIN_VERSION = '2.0.0';

    /**
     * @see WatcherInterface::init()
     */
    public function init(array $conf, HookHandler $handler = null)
    {
        $this->minVersion = $conf['version'] ?? self::DEFAULT_MIN_VERSION;
    }

    /**
     * @see WatcherInterface::check()
     */
    public function check(): bool
    {
        $version = system('git version|awk "{print \$3}"');
        return version_compare($version, $this->minVersion) >= 0;
    }
}
