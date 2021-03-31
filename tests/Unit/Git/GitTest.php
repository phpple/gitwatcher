<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/22 11:51 pm
 * @copyright: 2020@100tal.com
 * @filesource: CommitTest.php
 */

namespace Phpple\GitWatcher\Watcher\Tests\Unit\Git;

use Phpple\GitWatcher\Foundation\Git\GitUtil;
use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    public function testGetBranch()
    {
        $branch = 'foo';
        $expectBranch = 'refs/heads/foo';
        $this->assertEquals($expectBranch, GitUtil::getStandardBranch($branch));
        $this->assertEquals($expectBranch, GitUtil::getStandardBranch($expectBranch));
    }

    public function testGetCurrentBranch()
    {
        $expect = 'master';
        $expect = GitUtil::getStandardBranch($expect);
        $branch = GitUtil::getCurrentBranch(SITE_ROOT);
        $this->assertEquals($expect, $branch);
    }
}