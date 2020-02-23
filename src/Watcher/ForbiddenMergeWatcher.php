<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/22 9:51 上午
 * @copyright: 2020@100tal.com
 * @filesource: ForbiddenMergeWatcher.phpher.php
 */

namespace Phpple\GitWatcher\Watcher;


class ForbiddenMergeWatcher implements WatcherInterface
{

    /**
     * 配置项
     * @param array $conf
     * @return mixed
     */
    public function init(array $conf)
    {
        // TODO: Implement init() method.
    }

    /**
     * 检查是否通过
     * @return bool
     */
    public function check(): bool
    {
        return true;
    }
}