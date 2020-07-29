<?php
/**
 * @author Shubin Sergei <is.captain.fail@gmail.com>
 * @license MIT
 * 08.03.2020 2020
 */

namespace CFGit\BKit;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class CFServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("cf-git.bkit", function (Application $app) {
            return new BladeExtendor($app);
        });
    }

    public function boot()
    {
        $this->app->get("cf-git.bkit")->extendBladeDirectives();
    }
}
