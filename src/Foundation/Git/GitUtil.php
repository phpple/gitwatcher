<?php
/**
 *
 * @author: ronnie
 * @since: 2020/2/22 10:01 上午
 * @copyright: 2020@100tal.com
 * @filesource: GitUtil.php
 */

namespace Phpple\GitWatcher\Foundation\Git;

use Phpple\GitWatcher\Foundation\Util\ConsoleUtil;

class GitUtil
{
    const BRANCH_PREFIX = 'refs/heads/';
    const TAG_PREFIX = 'refs/tags/';
    // empty commit
    const ZERO_COMMIT = '0000000000000000000000000000000000000000';

    /**
     * Is based on the base commit
     * @param $dir
     * @param $baseCommit
     * @param $compareCommit
     * @return bool
     */
    public static function isBasedOnCommit(string $dir, string $baseCommit, string $compareCommit): bool
    {
        chdir($dir);
        $cmd = sprintf('git merge-base %s %s', $baseCommit, $compareCommit);
        ConsoleUtil::stdout($cmd);
        exec($cmd, $lines, $code);
        if ($code === 0) {
            return $lines[0] == $compareCommit;
        }
        return false;
    }

    /**
     * Get commits from standard input
     * @return HookCommits
     */
    public static function getCommitsFromStdin(): HookCommits
    {
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
     * Get standard branch name
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
     * Get current branch
     * @param string $dir
     * @return string
     */
    public static function getCurrentBranch(string $dir): string
    {
        chdir($dir);
        $file = realpath($dir . '/.git/HEAD');
        if (!$file) {
            return '';
        }
        return trim(explode(': ', file_get_contents($file), 2)[1]);
    }

    /**
     * Get commit by branch
     * @param string $dir
     * @param string $branch
     * @param bool $local
     * @return string
     */
    public static function getBranchCommit(string $dir, string $branch, bool $local = true): string
    {
        chdir($dir);
        if ($local) {
            $cmd = "git show-ref --heads ${branch}|awk '{print $1}'";
            ConsoleUtil::stdout($cmd);
            exec($cmd, $outputs, $code);
            if ($code === 0) {
                return trim($outputs[0]);
            }
            return false;
        }

        $cmd = "cat .git/FETCH_HEAD |grep \"branch 'master'\"|awk '{print $1}'";
        ConsoleUtil::stdout($cmd);
        exec($cmd, $outputs, $code);
        if ($code === 0) {
            return trim($outputs[0]);
        }
        return false;
    }

    /**
     * Get branch name without prefix
     * @param $branch
     * @return mixed
     */
    public static function getBranchWithoutPrefix(string $branch): string
    {
        return str_replace(self::BRANCH_PREFIX, '', $branch);
    }

    /**
     * Get standard tag name
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
     * Get tag name without prefix
     * @param $tag
     * @return mixed
     */
    public static function getTagWithoutPrefix(string $tag): string
    {
        return str_replace(self::TAG_PREFIX, '', $tag);
    }

    /**
     * Get standard path
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
     * Get smart file size
     * @param int $size unit:Bytes
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
     * Get raw file
     * @param string $hash file's hash
     * @return string
     */
    public static function getRawFile(string $hash): string
    {
        return system('git show ' . $hash);
    }

    /**
     * Get updated files
     * @param string $dir
     * @return array
     */
    public static function getUpdatedFiles(string $dir): array
    {
        chdir($dir);
        $cmd = 'git diff --cached --name-only';
        exec($cmd, $outputs, $code);
        if ($code !== 0) {
            return [];
        }
        return $outputs;
    }

    /**
     * Get files's differents betwwen two versions
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
     * Is the first commit
     * @param $commit
     * @return bool
     */
    public static function isNoCommit($commit): bool
    {
        return $commit == self::ZERO_COMMIT;
    }
}
