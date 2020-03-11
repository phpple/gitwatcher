<?php
/**
 *
 * Author: abel
 * Date: 2020/3/12
 * Time: 00:28
 */

namespace Phpple\GitWatcher\Foundation\Laravel\Provider;

use Illuminate\Support\ServiceProvider;
use Phpple\GitWatcher\Foundation\Laravel\Command\InitCommand;

class GitWatcherProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //register laravel command
        if ($this->app->runningInConsole()) {
            $this->commands([
                InitCommand::class,
            ]);
        }
    }
}
