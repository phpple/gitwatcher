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
    private $minVersion;
    /**
     * 配置项
     * @param array $conf
     * @return mixed
     */
    public function init(array $conf)
    {
        $this->minVersion = $conf['version'];
    }

    /**
     * 检查是否通过
     * @return bool
     */
    public function check(): bool
    {
        $version = system('git version|awk "{print \$3}"');
        return version_compare($version, $this->minVersion) >= 0;
    }
}