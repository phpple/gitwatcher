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
    /**
     * Test Git Version Watcher
     */
    public function testGitVersionWatcher()
    {
        $handler = new HookHandler(SITE_ROOT);
        $loader = $handler->initWatcher(HookHandler::GIT_VERSION, []);
        $this->assertTrue($loader->check());
    }

    /**
     * Test pre commit
     * @throws \Exception
     */
    public function testPreCommit()
    {
        $handler = new HookHandler(SITE_ROOT, __DIR__ . '/files/rule.json');
        $this->assertTrue($handler->preCommit());
    }

    /**
     * Test Standard Watcher
     */
    public function testStandard()
    {
        $handler = new HookHandler(__DIR__ . '/files/');
        $loader = $handler->initWatcher(HookHandler::STANDARD, [
            'target' => __DIR__.'/files/',
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'standard' => 'PSR2',
        ]);
        $this->assertFalse($loader->check());

        $loader = $handler->initWatcher(HookHandler::STANDARD, [
            'target' => __DIR__.'/files/',
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'standard' => 'PSR2',
            'colors' => null,
        ]);
        $this->assertFalse($loader->check());
    }

    /**
     * Test standard with ignore option
     */
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

    /**
     * Test Standard Watcher by xml config file
     */
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

    /**
     * Test Commiter Watcher
     */
    public function testCommitterWatcher()
    {
        $originEmail = system('git config --get user.email');
        $pos = strpos($originEmail, '@');
        $originExt = substr($originEmail, $pos + 1);

        $handler = new HookHandler(SITE_ROOT);
        $loader = $handler->initWatcher(HookHandler::COMMITER, [
            'email_extension' => $originExt,
        ]);
        $this->assertTrue($loader->check());

        $randExt = uniqid().'.com';
        $loader2 = $handler->initWatcher(HookHandler::COMMITER, [
            'email_extension' => $randExt,
        ]);
        $this->assertFalse($loader2->check());
    }
}
