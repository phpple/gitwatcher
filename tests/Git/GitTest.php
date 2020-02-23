<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/22 11:51 下午
 * @copyright: 2020@100tal.com
 * @filesource: CommitTest.php
 */

namespace Phpple\GitWatcher\Watcher\Tests\Git;

use Phpple\GitWatcher\Foundation\Git\GitUtil;
use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    public function testCommitContain()
    {
        $dir = REPO_ROOT;
        $ret = GitUtil::isBasedOnCommit($dir, 'f8c9e44', '9f110fa');
        $this->assertTrue($ret);
    }

    public function testGetBranch()
    {
        $branch = 'foo';
        $expectBranch = 'refs/heads/foo';
        $this->assertEquals($expectBranch, GitUtil::getStandardBranch($branch));
        $this->assertEquals($expectBranch, GitUtil::getStandardBranch($expectBranch));
    }
}