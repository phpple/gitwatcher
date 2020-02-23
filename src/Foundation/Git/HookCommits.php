<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 8:10 下午
 * @copyright: 2020@100tal.com
 * @filesource: HookCommitss.php
 */

namespace Phpple\GitWatcher\Foundation\Git;

class HookCommits
{
    private $commits = [];

    public function addCommit($start, $end, $branch)
    {
        $this->commits[] = [
            'start' => $start,
            'end' => $end,
            'branch' => $branch,
        ];
        return $this;
    }

    public function getCommits()
    {
        return $this->commits;
    }
}
