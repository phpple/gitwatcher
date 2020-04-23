<?php
/**
 *
 * @author: ronnie
 * @since: 2020/3/21 8:46 下午
 * @copyright: 2020@100tal.com
 * @filesource: CommitterWatcher.php
 */

namespace Phpple\GitWatcher\Watcher;

use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;
use Phpple\GitWatcher\HookHandler;
use Phpple\GitWatcher\WatcherInterface;

class CommitterWatcher implements WatcherInterface
{
    const EMAIL_EXTENSION = 'email_extension';
    const RET_NOT_EMAIL = -2;
    const RET_SUCCUSS = 1;
    const RET_FAILURE = -1;
    private $conf = [
        self::EMAIL_EXTENSION => '',
    ];

    /**
     * Initialize the watcher
     * @param array $conf
     * @param HookHandler $handler
     * @return mixed
     */
    public function init(array $conf, HookHandler $handler = null)
    {
        foreach ($conf as $key => $val) {
            $this->conf[$key] = $val;
        }
    }

    /**
     * check if pass the examine
     * @return bool
     */
    public function check(): bool
    {
        if (!empty($this->conf[self::EMAIL_EXTENSION])) {
            $ext = strtolower($this->conf[self::EMAIL_EXTENSION]);
            $ret = $this->checkEmailExtension($ext);
            switch ($ret) {
                case self::RET_SUCCUSS:
                    return true;
                case self::RET_FAILURE:
                    ConsoleUtil::error('Email\'s extension not match ' . $ext);
                    return false;
                case self::RET_NOT_EMAIL:
                    ConsoleUtil::error('Email is not found from your configure.');
                    return false;
            }
        }
        return true;
    }

    /**
     * Check email's extension
     * @param $ext
     * @return int
     */
    private function checkEmailExtension($ext)
    {
        // 获取git的email后缀
        exec('git config --get user.email', $outputs, $ret);
        if ($ret !== 0 || empty($outputs)) {
            return self::RET_NOT_EMAIL;
        }

        $email = strtolower(trim($outputs[0]));
        if (preg_match('#\@' . $ext . '$#', $email)) {
            return self::RET_SUCCUSS;
        }
        return self::RET_FAILURE;
    }
}
