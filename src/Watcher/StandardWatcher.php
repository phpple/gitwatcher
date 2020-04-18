<?php
/**
 * Watcher for code's standard
 * @author: ronnie
 * @since: 2020/2/23 10:24 pm
 * @copyright: 2020@100tal.com
 * @filesource: StandardWatcher.php
 */

namespace Phpple\GitWatcher\Watcher;

use Phpple\GitWatcher\Foundation\Git\GitUtil;
use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;
use Phpple\GitWatcher\HookHandler;
use Phpple\GitWatcher\WatcherInterface;

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
    const STANDARD_KEY = 'standard';
    const MODE_KEY = 'mode';
    const PROJECT_ROOT_VAR = '{$project.root}';

    const MODE_ALL = 'all';
    const MODE_ONLY_UPDATE = 'update';

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
        if (!$phpcsPath) {
            ConsoleUtil::stderr('phpcs not found:' . $phpcsPath);
            return false;
        }

        $files = $this->getFiles();
        if (empty($files)) {
            ConsoleUtil::stderr('no need to check files');
            return true;
        }

        $options = $this->getOptions();

        $cmd = sprintf(
            '%s %s %s',
            $phpcsPath,
            empty($options) ? '' : implode(' ', $options),
            implode(' ', $files)
        );
        ConsoleUtil::stdout('cmd:' . $cmd);
        return !$this->execCommand($cmd, key_exists('colors', $options));
    }

    private function getFiles()
    {
        $targetDir = $this->conf['target'] ?? './';
        $targetDirs = array_map(function ($item) {
            return trim($item, '\s\/');
        }, explode(',', $targetDir));
        $dirs = [];
        foreach ($targetDirs as $dir) {
            $realDir = realpath($dir);
            if ($realDir) {
                $dirs[] = $dir . '/';
            }
        }
        if (empty($dirs)) {
            return [];
        }

        // Need to check updated files if mode is `update`
        $mode = $this->conf[self::MODE_KEY] ?? self::MODE_ALL;
        if ($mode === self::MODE_ALL) {
            return $dirs;
        }

        $files = GitUtil::getUpdatedFiles($this->handler->getRootDir());
        $results = [];
        foreach ($dirs as $dir) {
            $len = strlen($dir);
            foreach ($files as $key => $file) {
                if (strncmp($file, $dir, $len) === 0) {
                    $results[] = $file;
                    unset($files[$key]);
                }
            }
        }

        return $results;
    }

    /**
     * Get options of phpcs
     * @return array
     */
    private function getOptions()
    {
        $rawOptions = $this->conf['options'] ?? [];
        // handle standard
        $standard = $rawOptions[self::STANDARD_KEY] ?? '';
        if ($standard && strtolower(pathinfo($standard, PATHINFO_EXTENSION)) == 'xml') {
            $standard = str_replace(self::PROJECT_ROOT_VAR, dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR, $standard);
            $standard = realpath($standard);
        }
        if (!$standard) {
            $standard = self::DEFAULT_STANDARD;
        }
        $rawOptions[self::STANDARD_KEY] = $standard;

        // init options
        foreach (self::KV_CONF_KEYS as $key) {
            if (isset($rawOptions[$key])) {
                $options[$key] = sprintf('--%s="%s"', $key, $rawOptions[$key]);
            }
        }
        foreach (self::K_CONF_KEYS as $key) {
            if (!key_exists($key, $rawOptions) || $rawOptions[$key] !== true) {
                continue;
            }
            if (strlen($key) == 1 || $key == 'vv' || $key == 'vvv') {
                $options[$key] = sprintf('-%s', $key);
            } else {
                $options[$key] = sprintf('--%s', $key);
            }
        }
        return $options;
    }

    /**
     * execute command and return if found errors
     * @param string $cmd
     * @return bool
     */
    private function execCommand(string $cmd, bool $isColor): bool
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
