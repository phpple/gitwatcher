<?php
/**
 * Composer's Event Handler
 * @author: ronnie
 * @since: 2020/3/5 5:35 pm
 * @copyright: 2020@100tal.com
 * @filesource: Composer
 */

namespace Phpple\GitWatcher;

use Composer\Script\Event;
use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;

class Composer
{
    const DEFAULT_CONFIG_FILE = 'gitwatcher.json';

    /**
     * Get base dir of project
     * @param Event $evt
     * @return string
     */
    private static function getBaseDir(Event $evt)
    {
        return dirname($evt->getComposer()->getConfig()->get('vendor-dir'));
    }

    /**
     * When autoload dump executed
     * @param Event $evt
     */
    public static function postAutoloadDump(Event $evt)
    {
        // base dir
        $baseDir = self::getBaseDir($evt);
        // hook file path
        $hookFile = $baseDir . '/.git/hooks/pre-commit';
        // copy hook file
        if (!is_dir(dirname($hookFile))) {
            return;
        }
        $originFile = dirname(__DIR__) . '/assets/hooks/pre-commit';

        if (is_file($hookFile) && (md5_file($hookFile) !== md5_file($originFile) || !is_executable($hookFile))) {
            ConsoleUtil::notice('hook file existed, replace it? y/n');
            $result = ConsoleUtil::stdin();
            if ($result == 'y') {
                unlink($hookFile);
            }
        }

        if (!is_file($hookFile)) {
            echo 'copy file assets/hooks/pre-commit' . PHP_EOL;

            copy($originFile, $hookFile);
            chmod($hookFile, 0755);
        }
    }

    /**
     * Composer script manual check
     * @param Event $evt
     * @throws \Exception
     */
    public static function manualCheck(Event $evt)
    {
        $siteRoot = self::getBaseDir($evt);

        $handler = new HookHandler($siteRoot);
        $ret = $handler->preCommit();
        ConsoleUtil::stdout(PHP_EOL);
        if (!$ret) {
            ConsoleUtil::error('GitWatcher check failed!');
            exit(1);
        }
        ConsoleUtil::success('GitWatcher check passed!');
    }
}
