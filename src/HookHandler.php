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
use Phpple\GitWatcher\Watcher\WatcherInterface;

class HookHandler
{
    private $dir;
    private $confFile;
    private $confs = null;

    const FORBIDDEN_MERGE = 'forbidden_merge';
    const GIT_VERSION = 'git_version';
    const STANDARD = 'standard';
    const COMMITER = 'committer';

    const DEFAULT_CONF_FILE = 'gitwatcher.json';

    const WATCHER_LIST = [
        self::GIT_VERSION => [
            'version' => '2.0.0'
        ],
        self::COMMITER => [
            'email_extension' => ''
        ],
        self::FORBIDDEN_MERGE => [],
        self::STANDARD => [
            'standard' => 'PSR2',
        ],
    ];

    public function __construct(string $dir, string $confFile = '')
    {
        $this->dir = $dir;

        if (!$confFile) {
            $confFile = realpath($dir . '/' . self::DEFAULT_CONF_FILE);
        }
        if (!$confFile) {
            $confFile = realpath(dirname(__DIR__) . '/assets/' . self::DEFAULT_CONF_FILE);
        }
        $this->confFile = $confFile;

        if (is_file($confFile)) {
            $content = file_get_contents($confFile);
            $this->confs = json_decode($content, true);
        }
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
     * @param string $name
     * @return WatcherInterface
     */
    public function initWatcher(string $name, array $conf)
    {
        $className = __NAMESPACE__ . "\\Watcher\\" . str_replace('_', '', ucwords($name, '_')) . 'Watcher';
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
        $confs = self::WATCHER_LIST;
        if ($this->confs !== null) {
            $confs = $this->confs;
        }
        foreach ($confs as $name => $conf) {
            chdir($this->dir);
            $watcher = $this->initWatcher($name, $conf);
            if (!$watcher) {
                ConsoleUtil::stderr('watcher not found:' . $name);
                continue;
            }
            ConsoleUtil::stdout('-------watcher:' . $name . ' start ------');
            $ret = $watcher->check();
            ConsoleUtil::stdout('-------watcher:' . $name . ' end ------');
            if ($ret === false) {
                return false;
            }
        }
        return true;
    }
}
