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
        exec('find . -name *.php -type f', $files, $code);
        if ($code == 0) {
            foreach($files as $file) {
                ConsoleUtil::stdout('check file '.$file);
                exec(sprintf('php -nl %s', $file), $outputs, $code);
                if ($code !== 0) {
                    ConsoleUtil::stderr($outputs);
                    return false;
                }
            }
        }
        return true;
    }
}