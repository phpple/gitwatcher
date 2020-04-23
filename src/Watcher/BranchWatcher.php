<?php
/**
 *
 * @author: ronnie
 * @since: 2020/3/22 1:36 下午
 * @copyright: 2020@100tal.com
 * @filesource: BranchWatcher
 */

namespace Phpple\GitWatcher\Watcher;

use Phpple\GitWatcher\Foundation\Git\GitUtil;
use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;
use Phpple\GitWatcher\HookHandler;
use Phpple\GitWatcher\WatcherInterface;

class BranchWatcher implements WatcherInterface
{
    const DEFAULT_COMPARE_BRANCH = 'master';
    /**
     * @var string $rootDir
     */
    private $rootDir;
    private $conf = [
        'rebase' => [
            'compare' => self::DEFAULT_COMPARE_BRANCH,
            'excludes' => 'dev,test,demo,' . self::DEFAULT_COMPARE_BRANCH
        ]
    ];

    /**
     * Initialize the watcher
     * @param array $conf
     * @param HookHandler $handler
     * @return mixed
     */
    public function init(array $conf, HookHandler $handler = null)
    {
        $this->rootDir = $handler->getRootDir();
    }

    /**
     * check if pass the examine
     * @return bool
     */
    public function check(): bool
    {
        ConsoleUtil::notice('git fetch');
        system('git fetch', $var);
        if ($var !== 0) {
            ConsoleUtil::warn('fetch failed, continue? [n|y]');
            if (ConsoleUtil::stdin() == 'y') {
                return true;
            }
            return false;
        }
        if (isset($this->conf['rebase']) && !$this->checkRebase($this->conf['rebase'])) {
            return false;
        }
        return true;
    }

    /**
     * Check the rebase
     * @param $confs
     * @return bool
     */
    private function checkRebase($confs)
    {
        $currentBranch = GitUtil::getCurrentBranch($this->rootDir);
        $currentBranch = GitUtil::getBranchWithoutPrefix($currentBranch);

        $excludes = $confs['excludes'] ?? [];
        if ($excludes) {
            $excludes = array_map('trim', explode(',', $excludes));
        }
        if ($excludes && in_array($currentBranch, $excludes)) {
            ConsoleUtil::error('current branch ignored');
            return true;
        }

        $compareBranch = $confs['compare'] ?? self::DEFAULT_COMPARE_BRANCH;
        $compareCommit = GitUtil::getBranchCommit($this->rootDir, $compareBranch, false);
        $ret = GitUtil::isBasedOnCommit($this->rootDir, $currentBranch, $compareCommit);
        if (!$ret) {
            ConsoleUtil::error('current branch is not based on ' . $compareBranch . PHP_EOL);
            ConsoleUtil::error('you should execute command: ' . PHP_EOL . 'git rebase ' . $compareCommit . PHP_EOL);
        }
        return $ret;
    }
}
