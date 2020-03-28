<?php
/**
 * Watcher for code's standard
 * @author: ronnie
 * @since: 2020/2/23 10:24 pm
 * @copyright: 2020@100tal.com
 * @filesource: StandardWatcher.php
 */

namespace Phpple\GitWatcher\Watcher;

use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;
use Phpple\GitWatcher\HookHandler;

class StandardWatcher implements WatcherInterface
{
    private $conf = [];
    /**
     * @var HookHandler
     */
    private $handler;
    const DEFAULT_STANDARD = 'PSR2';
    const DEFAULT_PHPCS_PATH = './vendor/bin/phpcs';
    const KV_CONF_KEYS = [
        'cache',
        'tab-width',
        'report',
        'report-file',
        'basepath',
        'bootstrap',
        'standard',
        'sniffs',
        'exclude',
        'encoding',
        'extensions',
        'ignore',
        'file-list',
        'filter',
    ];
    const K_CONF_KEYS = [
        's',
        'colors',
        'no-colors',
        'v',
        'vv',
        'vvv',
    ];

    /**
     * @see WatcherInterface::init()
     */
    public function init(array $conf, HookHandler $handler = null)
    {
        $this->handler = $handler;
        $this->conf = $conf;
    }

    /**
     * @see WatcherInterface::check()
     */
    public function check(): bool
    {
        $siteRoot = $this->handler->getRootDir();
        $confFile = $this->handler->getConfigFile();
        // handle phpcs
        $phpcsPath = $this->conf['phpcs'] ?? '';
        if ($phpcsPath) {
            if (substr($phpcsPath, 0, 1) != '/' && defined('SITE_ROOT')) {
                $phpcsPath = $siteRoot . '/' . $phpcsPath;
            }
        } else {
            $phpcsPath = self::DEFAULT_PHPCS_PATH;
        }
        $phpcsPath = realpath($phpcsPath);

        // handle standard
        $standard = $this->conf['standard'] ?? '';
        if ($standard && strtolower(pathinfo($standard, PATHINFO_EXTENSION)) == 'xml') {
            if (realpath($standard) === false) {
                $standard = realpath(dirname($confFile) . '/' . $standard);
            } else {
                $standard = realpath($standard);
            }
        }
        if (!$standard) {
            $standard = self::DEFAULT_STANDARD;
        }
        $this->conf['standard'] = $standard;

        // init options
        $options = [];
        foreach (self::KV_CONF_KEYS as $key) {
            if (isset($this->conf[$key])) {
                $options[$key] = sprintf('--%s="%s"', $key, $this->conf[$key]);
            }
        }
        foreach (self::K_CONF_KEYS as $key) {
            if (key_exists($key, $this->conf)) {
                if (strlen($key) == 1 || $key == 'vv' || $key == 'vvv') {
                    $options[$key] = sprintf('-%s', $key);
                } else {
                    $options[$key] = sprintf('--%s', $key);
                }
            }
        }

        $targetDir = $this->conf['target'] ?? './';
        $targetDir = realpath($targetDir);

        if (!$phpcsPath) {
            ConsoleUtil::stderr('phpcs not found:' . $phpcsPath);
            return false;
        }
        $cmd = sprintf(
            '%s %s %s',
            $phpcsPath,
            empty($options) ? '' : implode(' ', $options),
            $targetDir
        );
        ConsoleUtil::stdout('cmd:' . $cmd);
        return !$this->execCommand($cmd, key_exists('colors', $this->conf));
    }

    /**
     * execute command and return if found errors
     * @param string $cmd
     * @return bool
     */
    private function execCommand(string $cmd, bool  $isColor): bool
    {
        $haveErrors = false;
        $ph = popen($cmd, 'r');
        $prefix = 'FOUND ';
        $prefixLen = strlen($prefix);
        if ($isColor) {
            $prefix = "\033[1mFOUND ";
            $prefixLen = strlen($prefix);
        }
        while (!feof($ph)) {
            $line = fgets($ph);
            if (!$haveErrors && strncmp($line, $prefix, $prefixLen) === 0 && $line[$prefixLen] !== '0') {
                $haveErrors = true;
            }
            fputs(STDOUT, $line);
        }
        fclose($ph);
        return $haveErrors;
    }
}
