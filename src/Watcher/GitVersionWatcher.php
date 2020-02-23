<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 12:35 上午
 * @copyright: 2020@100tal.com
 * @filesource: GitVersionWatcher.php
 */

namespace Phpple\GitWatcher\Watcher;


class GitVersionWatcher implements WatcherInterface
{
    const MIN_VERSION = '2.0.0';
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
        $version = system('git version|awk "{print \$3}"');
        return version_compare($version, self::MIN_VERSION) >= 0;
    }
}