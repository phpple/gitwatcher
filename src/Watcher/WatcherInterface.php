<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/22 9:52 上午
 * @copyright: 2020@100tal.com
 * @filesource: WatcherInterface.php
 */

namespace Phpple\GitWatcher\Watcher;


interface WatcherInterface
{
    /**
     * 配置项
     * @param array $conf
     * @return mixed
     */
    public function init(array $conf);

    /**
     * 检查是否通过
     * @return bool
     */
    public function check(): bool;
}