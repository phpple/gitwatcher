<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/23 8:03 下午
 * @copyright: 2020@100tal.com
 * @filesource: FileUtilUtil.php
 */

namespace Phpple\GitWatcher\Foundation\Util;


class FileUtil
{
    /**
     * 获取文件的扩展名
     * @param $path
     * @return bool|string
     */
    public static function getExt(string $path): string
    {
        if (($pos = strrpos($path, '.')) !== false) {
            return substr($path, $pos + 1);
        }
        return false;
    }
}