<?php
/**
 *
 * @author: ronnie
 * @since: 2020/3/28 9:40 下午
 * @copyright: 2020@100tal.com
 * @filesource: ComposerWatcher.php
 */

namespace Phpple\GitWatcher\Watcher;

use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;
use Phpple\GitWatcher\HookHandler;
use Phpple\GitWatcher\WatcherInterface;

class ComposerWatcher implements WatcherInterface
{
    /**
     * @var composer's configure file
     */
    private $composerFile;

    /**
     * @var array exclude requires
     */
    const EXCLUDE_REQUIRES = [
        'php',
    ];

    const VERSION_REGEXP = '#^[^\*~><=\^\|\s]+$#';

    /**
     * Initialize the watcher
     * @param array $conf
     * @param HookHandler $handler
     * @return mixed
     */
    public function init(array $conf, HookHandler $handler = null)
    {
        $this->composerFile = $handler->getRootDir() . '/composer.json';
    }

    /**
     * check if pass the examine
     * @return bool
     */
    public function check(): bool
    {
        $path = realpath($this->composerFile);
        if (!$path) {
            ConsoleUtil::error('composer file not found:' . $this->composerFile);
            return false;
        }

        $configs = json_decode(file_get_contents($path), true);
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            ConsoleUtil::error('composer file parse failed:' . $error);
            return false;
        }

        // exclude some requires
        $requires = $configs['require'] ?? [];
        if (!is_array($requires)) {
            ConsoleUtil::error('composer file require is illegal');
            return false;
        }

        $passed = true;
        foreach ($requires as $key => $val) {
            if (in_array($key, self::EXCLUDE_REQUIRES)) {
                continue;
            }

            if (!self::isConstVersion($val)) {
                $passed = false;
                ConsoleUtil::error(sprintf('[%s]: %s is not a constant version', $key, $val));
            }
        }
        return $passed;
    }

    /**
     * 是否为常量的版本
     * @param $version
     * @return false|int
     */
    public static function isConstVersion($version)
    {
        return !!preg_match(self::VERSION_REGEXP, $version);
    }
}
