<?php
/**
 * Watcher for forbidden merge
 * @author: ronnie
 * @since: 2020/2/22 9:51 am
 * @copyright: 2020@100tal.com
 * @filesource: ForbiddenMergeWatcher.phpher.php
 */

namespace Phpple\GitWatcher\Watcher;

use Phpple\GitWatcher\HookHandler;

class ForbiddenMergeWatcher implements WatcherInterface
{
    /**
     * @see WatcherInterface::init()
     */
    public function init(array $conf, HookHandler $handler = null)
    {
        // TODO: Implement init() method.
    }

    /**
     * @see WatcherInterface::check()
     */
    public function check(): bool
    {
        return true;
    }
}
