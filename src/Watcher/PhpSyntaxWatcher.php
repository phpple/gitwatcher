<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 9:36 下午
 * @copyright: 2020@100tal.com
 * @filesource: PhpSyntaxWatcherWatcher.php
 */

namespace Phpple\GitWatcher\Watcher;

use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;
use SebastianBergmann\Environment\Console;

class PhpSyntaxWatcher implements WatcherInterface
{
    private $conf = [];

    const DEFAULT_EXT = '*.php';
    const DEFAULT_DIR = './';

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
        $ext = $this->conf['extension'] ?? self::DEFAULT_EXT;
        $dest = $this->conf['dir'] ?? self::DEFAULT_DIR;
        $cmd = sprintf('find %s -name "%s" -type f', $dest, $ext);
        ConsoleUtil::stdout($cmd);
        exec($cmd, $files, $code);
        $passed = true;
        if ($code == 0) {
            foreach ($files as $file) {
                ConsoleUtil::stdout('check file ' . $file);
                exec(sprintf('php -nl %s', $file), $outputs, $code);
                if ($code !== 0) {
                    ConsoleUtil::stderr($outputs);
                    $passed = false;
                }
            }
        }
        return $passed;
    }
}
