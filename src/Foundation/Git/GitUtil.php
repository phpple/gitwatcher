<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/22 10:01 上午
 * @copyright: 2020@100tal.com
 * @filesource: GitUtil.php
 */

namespace Phpple\GitWatcher\Foundation\Git;

class GitUtil
{
    const BRANCH_PREFIX = 'refs/heads/';
    const TAG_PREFIX = 'refs/tags/';
    // 空提交
    const ZERO_COMMIT = '0000000000000000000000000000000000000000';

    /**
     * 是否基于某个基准提交
     * @param $dir
     * @param $baseCommit
     * @param $compareCommit
     * @return bool
     */
    public static function isBasedOnCommit(string $dir, string $baseCommit, string $compareCommit): bool
    {
        chdir($dir);
        $cmd = sprintf('git log %s --format="%s" | grep "%s" ', $baseCommit, '%H', $compareCommit);
        exec($cmd, $lines, $code);
        return !empty($lines);
    }

    /**
     * 从标准输入获取提交
     * @return HookCommits
     */
    public static function getCommitsFromStdin(): HookCommits
    {
        $commits = array();

        $hookCommits = new HookCommits();
        while (!feof(STDIN)) {
            $line = trim(fgets(STDIN));
            if (!$line) {
                continue;
            }
            $hookCommits->addCommit(explode(' ', $line));
        }
        return $hookCommits;
    }


    /**
     * 获取标准的分支名称
     * @param $branch
     * @return string
     */
    public static function getStandardBranch(string $branch): string
    {
        if (preg_match('#^[a-z0-9]{40}$#', $branch)) {
            return $branch;
        } elseif (strpos($branch, self::BRANCH_PREFIX) !== false) {
            return $branch;
        }
        return self::BRANCH_PREFIX . $branch;
    }

    /**
     * 获取去掉前缀的分支
     * @param $branch
     * @return mixed
     */
    public static function getBranchWithoutPrefix(string $branch): string
    {
        return str_replace(self::BRANCH_PREFIX, '', $branch);
    }

    /**
     * 获取标准的标签名称
     * @param $tag
     * @return string
     */
    public static function getStandardTag(string $tag): string
    {
        if (strpos($tag, self::TAG_PREFIX) !== false) {
            return $tag;
        }
        return self::TAG_PREFIX . $tag;
    }


    /**
     * 获取去掉前缀的标签
     * @param $tag
     * @return mixed
     */
    public static function getTagWithoutPrefix(string $tag): string
    {
        return str_replace(self::TAG_PREFIX, '', $tag);
    }

    /**
     * 将git的路径调整为标准的路径
     * @param $path
     * @return string
     */
    public static function getStandardPath(string $path): string
    {
        if (preg_match('#^"(.+)"$#', $path, $matches)) {
            $path = $matches[1];
            $path = preg_replace_callback('#\\\[0-7]{3}#', function ($ms) {
                $number = base_convert(intval(substr($ms[0], 1)), 8, 16);
                return '%' . strtoupper($number);
            }, $matches[1]);
            $path = rawurldecode($path);
        }
        return $path;
    }

    /**
     * 将文件大小已智能化方式显示
     * @param int $size 字节
     * @return string
     */
    public static function getSmartSize(int $size): string
    {
        if ($size > 1000 * 1024) {
            $size = sprintf('%.2fM', $size / (1024 * 1024));
        } elseif ($size > 1024) {
            $size = sprintf('%.2fk', $size / 1024);
        } else {
            $size = $size . 'b';
        }
        return $size;
    }

    /**
     * 获取原始文件
     * @param string $hash 文件HASH
     * @return string
     */
    public static function getRawFile(string $hash): string
    {
        return system('git show ' . $hash);
    }

    /**
     * 获取两个版本的文件变化
     * @param string $oldrev
     * @param string $newrev
     * @return array
     */
    public static function getDiffFiles(string $oldrev, string $newrev): string
    {
        if ($oldrev == str_repeat('0', 40)) {
            $cmd = "git ls-tree -rl {$newrev}";
            exec($cmd, $outputs);
            $results = array();
            foreach ($outputs as $line) {
                list($newMod, $type, $newHash, $size, $path) = preg_split('#[\t\s]+#', $line);

                $results['A'][] = array(
                    'path' => self::normalPath($path),
                    'hash' => $newHash,
                );
            }
        } else {
            $cmd = "git diff-tree {$oldrev}..{$newrev}";
            exec($cmd, $outputs);
            $results = array();
            foreach ($outputs as $line) {
                list($oldMod, $newMod, $oldHash, $newHash, $type, $path) = preg_split('#[\t\s]+#', $line);
                $results[$type][] = array(
                    'path' => self::normalPath($path),
                    'hash' => $newHash,
                );
            }
        }
        return $results;
    }

    /**
     * 是否为初始提交
     * @param $commit
     * @return bool
     */
    public static function isNoCommit($commit): bool
    {
        return $commit == self::ZERO_COMMIT;
    }
}
