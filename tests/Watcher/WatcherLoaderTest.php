<?php
/**
 * Test for watcher loader
 * @author: ronnie
 * @since: 2020/2/23 9:26 pm
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
        $handler = new HookHandler(SITE_ROOT);
        $loader = $handler->initWatcher(HookHandler::GIT_VERSION, []);
        $this->assertTrue($loader->check());
    }

    public function testPreCommit()
    {
        $handler = new HookHandler(SITE_ROOT, __DIR__ . '/files/rule.json');
        $this->assertTrue($handler->preCommit());
    }

    public function testStandard()
    {
        $handler = new HookHandler(__DIR__ . '/files/');
        $loader = $handler->initWatcher(HookHandler::STANDARD, [
            'target' => __DIR__.'/files/',
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'standard' => 'PSR2',
        ]);
        $this->assertFalse($loader->check());
    }

    public function testStandardWithIgnore()
    {
        $handler = new HookHandler(SITE_ROOT);
        $loader = $handler->initWatcher(HookHandler::STANDARD, [
            'target' => __DIR__.'/files/',
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'standard' => 'PSR2',
            'ignore' => 'badstandard.php'
        ]);
        $this->assertTrue($loader->check());
    }

    public function testStandardWithXml()
    {
        $handler = new HookHandler(SITE_ROOT);
        $loader = $handler->initWatcher(HookHandler::STANDARD, [
            'target' => __DIR__,
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'standard' => SITE_ROOT . '/assets/rules/phpdefault.xml',
            's' => null,
        ]);
        $this->assertFalse($loader->check());
    }
}
