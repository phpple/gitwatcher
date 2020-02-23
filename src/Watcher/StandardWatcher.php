<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 10:24 下午
 * @copyright: 2020@100tal.com
 * @filesource: StandardWatcher.php
 */

namespace Phpple\GitWatcher\Watcher;


use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;

class StandardWatcher implements WatcherInterface
{
    private $conf = [];

    /**
     * 配置项
     * @param array $conf
     * @return mixed
     */
    public function init(array $conf)
    {
        $this->conf = $conf;
    }

    /**
     * 检查是否通过
     * @return bool
     */
    public function check(): bool
    {
        $phpcsPath = $this->conf['phpcs'] ?? realpath('vendor/bin/phpcs');
        $standard = $this->conf['standard'] ?? 'PSR2';
        if (!is_executable($phpcsPath)) {
            ConsoleUtil::stderr('phpcs not found:'.$phpcsPath);
            return false;
        }
        $cmd = sprintf('%s --standard=%s --colors ./', $phpcsPath, $standard);
        ConsoleUtil::stdout('cmd:'. $cmd);
        system($cmd, $code);
        if ($code !== 0) {
            return false;
        }
    }
}