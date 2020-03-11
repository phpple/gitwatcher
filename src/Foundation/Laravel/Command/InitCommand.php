<?php
/**
 *
 * Author: abel
 * Date: 2020/3/12
 * Time: 00:28
 */


namespace Phpple\GitWatcher\Foundation\Laravel\Command;


use Illuminate\Console\Command;

class InitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gitwatcher:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $basePath = base_path();
        if ( ! file_exists("${basePath}/gitwatcher.json")) {
            if ( ! copy("${basePath}/vendor/phpple/gitwatcher/assert/gitwatcher.json", "${basePath}/gitwatcher.json")) {
                echo 'Create config failed.Please create gitwatcher.json manually.';

                return;
            }
            @chmod("${basePath}/gitwatcher.json", 0755);
        }

        if (is_dir("${basePath}/.git/hooks")) {
            if (! file_exists("${basePath}/.git/hooks/pre-commit") && ! copy("${basePath}/vendor/phpple/gitwatcher/bin/hooks/pre-commit", "${basePath}/.git/hooks/pre-commit")) {
                echo "Create git hook failed.Please copy '${basePath}/vendor/phpple/gitwatcher/bin/hooks/pre-commit' to '.git/hooks/pre-commit' manually.";
                return;
            }
            @chmod("${basePath}/.git/hooks/pre-commit", 0755);
        }else{
            echo "This is not a git repository. Please retry after execute command(git init).";
        }
    }
}