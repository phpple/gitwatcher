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

    const VERSION_REGEXP = '#^\d+(\.\d+)*$#';

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
            ConsoleUtil::stderr('composer file not found:' . $this->composerFile);
            return false;
        }

        $configs = json_decode(file_get_contents($path), true);
        $error = json_last_error();
        if ($error !== JSON_ERROR_NONE) {
            ConsoleUtil::stderr('composer file parse failed:' . $error);
            return false;
        }

        // exclude some requires
        $requires = $configs['require'] ?? [];
        if (!is_array($requires)) {
            ConsoleUtil::stderr('composer file require is illegal');
            return false;
        }

        $passed = true;
        foreach ($requires as $key => $val) {
            if (in_array($key, self::EXCLUDE_REQUIRES)) {
                continue;
            }

            if (!preg_match(self::VERSION_REGEXP, $val)) {
                $passed = false;
                ConsoleUtil::stderr(sprintf('[%s]: %s is not a constant', $key, $val));
            }
        }
        return $passed;
    }
}
