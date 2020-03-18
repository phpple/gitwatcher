<?php
/**
 * Watcher Interface
 * @author: ronnie
 * @since: 2020/2/22 9:52 am
 * @copyright: 2020@100tal.com
 * @filesource: WatcherInterface.php
 */

namespace Phpple\GitWatcher\Watcher;

use Phpple\GitWatcher\HookHandler;

interface WatcherInterface
{
    /**
     * Initialize the watcher
     * @param array $conf
     * @param HookHandler $handler
     * @return mixed
     */
    public function init(array $conf, HookHandler $handler = null);

    /**
     * check if pass the examine
     * @return bool
     */
    public function check(): bool;
}
