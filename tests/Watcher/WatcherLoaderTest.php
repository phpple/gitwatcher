<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 9:26 下午
 * @copyright: 2020@100tal.com
 * @filesource: WatcherLoaderTest.php
 */

namespace Phpple\GitWatcher\Tests\Watcher;


use Phpple\GitWatcher\HookHandler;
use PHPUnit\Framework\TestCase;

class WatcherLoaderTest extends TestCase
{
    public function testGitVersionLoader()
    {
        $loader = HookHandler::initWatcher(HookHandler::GIT_VERSION, []);
        $this->assertTrue($loader->check());
    }

    public function testPreCommit()
    {
        $ret = HookHandler::preCommit(SITE_ROOT.'/src/');
        $this->assertTrue($ret);
    }

    public function testPhpSyntax()
    {
        chdir(__DIR__.'/files/');
        $loader = HookHandler::initWatcher(HookHandler::PHP_SYNTAX, []);
        $this->assertFalse($loader->check());
    }
}