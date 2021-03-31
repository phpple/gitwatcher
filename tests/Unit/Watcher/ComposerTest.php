<?php

declare(strict_types=1);
/**
 *
 * @author:     ronnie
 * @since:      2021/3/31 2:42 下午
 * @copyright:  2021@julive.com
 * @filesource: ComposerTest.php
 */
namespace Unit\Watcher;

use Phpple\GitWatcher\Watcher\ComposerWatcher;
use PHPUnit\Framework\TestCase;

class ComposerTest extends TestCase
{
    public function testConstVersion()
    {
        $illegalVersions = [
            '^1.3.4',
            '~1.3.2',
            '*',
            '1.3.*',
            '>1.3.0',
            '<=34.3',
        ];

        foreach($illegalVersions as $version) {
            $this->assertFalse(ComposerWatcher::isConstVersion($version));
        }

        $normalVersions = [
            '0.2.6',
            '0.2.5-beta',
            'v4.23.6',
            'version.5.7.0',
        ];

        foreach($normalVersions as $version) {
            $this->assertTrue(ComposerWatcher::isConstVersion($version));
        }
    }
}