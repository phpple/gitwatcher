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
        $handler = new HookHandler(SITE_ROOT . '/src/', __DIR__ . '/files/rule.json');
        $this->assertTrue($handler->preCommit());
    }

    public function testPhpSyntax()
    {
        $loader = HookHandler::initWatcher(HookHandler::PHP_SYNTAX, [
            'dir' => __DIR__ . '/files/',
        ]);
        $this->assertFalse($loader->check());

        $loader = HookHandler::initWatcher(HookHandler::PHP_SYNTAX, [
            'dir' => __DIR__ . '/files/',
            'exclude' => 'badsyntax'
        ]);
        $this->assertTrue($loader->check());
    }

    public function testStandard()
    {
        chdir(__DIR__ . '/files/');
        $loader = HookHandler::initWatcher(HookHandler::STANDARD, [
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'standard' => 'PSR2',
        ]);
        $this->assertFalse($loader->check());
    }

    public function testStandardWithIgnore()
    {
        $loader = HookHandler::initWatcher(HookHandler::STANDARD, [
            'target' => __DIR__.'/files/',
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'standard' => 'PSR2',
            'ignore' => 'badstandard.php'
        ]);
        $this->assertTrue($loader->check());
    }

    public function testStandardWithXml()
    {
        $loader = HookHandler::initWatcher(HookHandler::STANDARD, [
            'target' => __DIR__,
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'standard' => SITE_ROOT . '/rules/phpdefault.xml',
            's' => null,
        ]);
        $this->assertFalse($loader->check());
    }
}
