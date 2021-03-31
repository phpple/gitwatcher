<?php
/**
 * Test for watcher loader
 * @author: ronnie
 * @since: 2020/2/23 9:26 pm
 * @copyright: 2020@100tal.com
 * @filesource: WatcherLoaderTest.php
 */

namespace Phpple\GitWatcher\Tests\Feature\Watcher;

use Phpple\GitWatcher\HookHandler;
use Phpple\GitWatcher\Watcher\CommitterWatcher;
use Phpple\GitWatcher\Watcher\ComposerWatcher;
use Phpple\GitWatcher\Watcher\GitVersionWatcher;
use Phpple\GitWatcher\Watcher\StandardWatcher;
use PHPUnit\Framework\TestCase;

class WatcherLoaderTest extends TestCase
{
    /**
     * Test Git Version Watcher
     */
    public function testGitVersionWatcher()
    {
        $handler = new HookHandler(SITE_ROOT);
        $loader = $handler->initWatcher(GitVersionWatcher::class, []);
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

        $handler = new HookHandler(SITE_ROOT, __DIR__ . '/files/extendrule.json');
        $confs = $handler->getConfs();
        $this->assertFalse($confs['standard']['options']['colors']);
    }

    /**
     * Test Standard Watcher
     */
    public function testStandard()
    {
        $handler = new HookHandler(__DIR__ . '/files/');
        $loader = $handler->initWatcher(StandardWatcher::class, [
            'target' => __DIR__ . '/files/',
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'options' => [
                'standard' => 'PSR2',
            ]
        ]);
        $this->assertFalse($loader->check());

        $loader = $handler->initWatcher(StandardWatcher::class, [
            'target' => __DIR__ . '/files/',
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'options' => [
                'standard' => 'PSR2',
                'colors' => null,
            ]
        ]);
        $this->assertFalse($loader->check());
    }

    /**
     * Test standard with ignore option
     */
    public function testStandardWithIgnore()
    {
        $handler = new HookHandler(SITE_ROOT);
        $loader = $handler->initWatcher(StandardWatcher::class, [
            'target' => __DIR__ . '/files/',
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'options' => [
                'standard' => 'PSR2',
                'ignore' => 'badstandard.php'
            ]
        ]);
        $this->assertTrue($loader->check());
    }

    /**
     * Test Standard Watcher by xml config file
     */
    public function testStandardWithXml()
    {
        $handler = new HookHandler(SITE_ROOT);
        $loader = $handler->initWatcher(StandardWatcher::class, [
            'target' => __DIR__,
            'phpcs' => SITE_ROOT . '/vendor/bin/phpcs',
            'options' => [
                'standard' => SITE_ROOT . '/assets/rules/phpdefault.xml',
                's' => null,
            ]
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
        $loader = $handler->initWatcher(CommitterWatcher::class, [
            'email_extension' => $originExt,
        ]);
        $this->assertTrue($loader->check());

        $randExt = uniqid() . '.com';
        $loader2 = $handler->initWatcher(CommitterWatcher::class, [
            'email_extension' => $randExt,
        ]);
        $this->assertFalse($loader2->check());
    }

    public function testComposerWatcher()
    {
        $dirs = [
            'notfound' => false,
            'parse' => false,
            'notconstant' => false,
            'correct' => true,
        ];
        foreach ($dirs as $dir => $expected) {
            $handler = new HookHandler(__DIR__ . '/composer/' . $dir);
            $loader = $handler->initWatcher(ComposerWatcher::class, []);
            $this->assertEquals($expected, $loader->check());
        }
    }
}
