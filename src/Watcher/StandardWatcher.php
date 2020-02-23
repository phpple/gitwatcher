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
    const CONF_KEYS = [
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
        foreach (self::CONF_KEYS as $key) {
            if (isset($this->conf[$key])) {
                $options[$key] = sprintf('--%s="%s"', $key, $this->conf[$key]);
            }
        }
        if (!isset($options['standard'])) {
            $options['standard'] = self::DEFAULT_STANDARD;
        }

        if (!$phpcsPath) {
            ConsoleUtil::stderr('phpcs not found:' . $phpcsPath);
            return false;
        }
        $cmd = sprintf(
            '%s %s --colors ./',
            $phpcsPath,
            empty($options) ? '' : implode(' ', $options)
        );
        ConsoleUtil::stdout('cmd:' . $cmd);
        system($cmd, $code);
        if ($code !== 0) {
            return false;
        }
        return true;
    }
}
