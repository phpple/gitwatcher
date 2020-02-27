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
        $phpcsPath = $this->conf['phpcs'] ?? '';
        if ($phpcsPath) {
            if (substr($phpcsPath, 0, 1) != '/' && defined('SITE_ROOT')) {
                $phpcsPath = SITE_ROOT . '/' . $phpcsPath;
            }
        } else {
            $phpcsPath = self::DEFAULT_PHPCS_PATH;
        }
        $phpcsPath = realpath($phpcsPath);

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
        if (!isset($options['standard'])) {
            $options['standard'] = '--standard=' . self::DEFAULT_STANDARD;
        }

        $targetDir = $this->conf['target'] ?? './';
        $targetDir = realpath($targetDir);

        if (!$phpcsPath) {
            ConsoleUtil::stderr('phpcs not found:' . $phpcsPath);
            return false;
        }
        $cmd = sprintf(
            '%s %s --colors %s',
            $phpcsPath,
            empty($options) ? '' : implode(' ', $options),
            $targetDir
        );
        ConsoleUtil::stdout('cmd:' . $cmd);
        system($cmd, $code);
        if ($code === 2) {
            return false;
        }
        return true;
    }
}
