<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 9:26 下午
 * @copyright: 2020@100tal.com
 * @filesource: WatcherLoaderTest.php
 */

namespace Phpple\GitWatcher\Tests\Watcher;


use Phpple\GitWatcher\Watcher\WatcherLoader;
use PHPUnit\Framework\TestCase;

class WatcherLoaderTest extends TestCase
{
    public function testGitVersionLoader()
    {
        $loader = WatcherLoader::initWatcher(WatcherLoader::GIT_VERSION, []);
        $this->assertTrue($loader->check());
    }

    public function testPreCommit()
    {
        $ret = WatcherLoader::preCommit();
        $this->assertTrue($ret);
    }

    public function testPhpSyntax()
    {
        chdir(__DIR__.'/files/');
        $loader = WatcherLoader::initWatcher(WatcherLoader::PHP_SYNTAX, []);
        $this->assertFalse($loader->check());
    }
}