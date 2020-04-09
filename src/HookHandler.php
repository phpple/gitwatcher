<?php
/**
 * Hook's Handler
 * @author: ronnie
 * @since: 2020/2/23 12:27 am
 * @copyright: 2020@100tal.com
 * @filesource: HookHandler.phpp
 */

namespace Phpple\GitWatcher;

use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;

class HookHandler
{
    private $dir;
    private $confFile;
    private $confs = [];

    const EXTEND_KEY = '@extend';

    const DEFAULT_CONF_FILE = 'gitwatcher.json';

    public function __construct(string $dir, string $confFile = '')
    {
        $this->dir = $dir;

        if (!$confFile) {
            $confFile = realpath($dir . DIRECTORY_SEPARATOR . self::DEFAULT_CONF_FILE);
        }
        if (!$confFile) {
            $confFile = realpath(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . self::DEFAULT_CONF_FILE);
        }
        $this->confFile = $confFile;

        if (is_file($confFile)) {
            $content = file_get_contents($confFile);
            $confs = json_decode($content, true);

            $this->loadExtend($confs);

            $this->confs = $confs;
            foreach ($this->confs as $key => $val) {
                if ($key[0] == '@') {
                    unset($this->confs[$key]);
                }
            }
        }
    }

    /**
     * Load extend file
     * @param $confs
     */
    private function loadExtend(&$confs)
    {
        if (empty($confs[self::EXTEND_KEY])) {
            return;
        }

        $extendVal = $confs[self::EXTEND_KEY];
        if ($extendVal === 'default') {
            $extendVal = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . self::DEFAULT_CONF_FILE;
        }

        $extend = realpath($extendVal);
        if (!is_file($extend)) {
            $extend = realpath(dirname($this->confFile) . DIRECTORY_SEPARATOR . $extendVal);
        }
        if (!is_file($extend)) {
            return;
        }
        $subConfs = json_decode(file_get_contents($extend), true);
        $confs = array_replace_recursive($subConfs, $confs);
    }

    /**
     * Get project's root dir
     * @return string
     */
    public function getRootDir()
    {
        return $this->dir;
    }

    /**
     * Get config file's path
     * @return false|string
     */
    public function getConfigFile()
    {
        return $this->confFile;
    }

    /**
     * Init loader
     * @param string $className
     * @return WatcherInterface
     */
    public function initWatcher(string $className, array $conf)
    {
        $watcher = new $className();
        if (!($watcher instanceof WatcherInterface)) {
            return null;
        }

        $watcher->init($conf, $this);
        return $watcher;
    }

    /**
     * pre-commit associated operation
     * @return bool
     * @throws \Exception
     */
    public function preCommit(): bool
    {
        foreach ($this->confs as $name => $conf) {
            chdir($this->dir);
            $className = __NAMESPACE__ . "\\Watcher\\" . str_replace('_', '', ucwords($name, '_')) . 'Watcher';
            if (!class_exists($className) || $conf === false) {
                continue;
            }
            $watcher = $this->initWatcher($className, $conf);
            if (!$watcher) {
                ConsoleUtil::stderr('watcher not found:' . $name);
                continue;
            }
            ConsoleUtil::stdout('-------watcher:' . $name . ' start ------');
            $ret = $watcher->check();
            ConsoleUtil::stdout('-------watcher:' . $name . ' end:' . ($ret ? 'true' : 'false') . ' ------');
            if ($ret === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取所有配置
     * @return array
     */
    public function getConfs()
    {
        return $this->confs;
    }
}
