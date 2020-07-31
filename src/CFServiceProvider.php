<?php
/**
 * @author Shubin Sergei <is.captain.fail@gmail.com>
 * @license GNU General Public License v3.0
 * 08.03.2020 2020
 */

namespace CFGit\BKit;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class CFServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->extend(\Illuminate\Contracts\View\Factory::class, function (
            \Illuminate\Contracts\View\Factory $factory,
            \Illuminate\Foundation\Application $app
        ) {
            $container = [];
            $factory->macro('startWrite', function ($variable) use (&$container) {
                $container[$variable] = "";
                ob_start();
            });
            $factory->macro('stopWrite', function () use (&$container) {
                $keys = array_reverse(array_keys($container));
                if (empty($keys)) {
                    throw new \InvalidArgumentException('Can\'t stop undefined variable writing.');
                }
                $container[$keys[0]] = ob_get_clean();
                return [$keys[0] => $container[$keys[0]]];
            });
            return $factory;
        });
        $this->app->singleton("cf-git.bkit", function (Application $app) {
            return new BladeExtendor($app);
        });
    }

    public function boot()
    {

        $this->app->get("cf-git.bkit")->extend("set", function ($variableName) {
            return "<?php
                \$__env->startWrite('{$variableName}');
            ?>";
        });
        $this->app->get("cf-git.bkit")->extend("endSet", function () {
            return "<?php
                \$__tmpCfGitBKitNewVars = \$__env->stopWrite();
                extract(\$__tmpCfGitBKitNewVars);
                unset(\$__tmpCfGitBKitNewVars);
            ?>";
        });

        $this->app->get("cf-git.bkit")->extend('pushOnce', function ($stackName, $pushName) {
            return "<?php
                if (is_null(config('__stacks__.{$stackName}.{$pushName}'))) {
                    config(['__stacks__.{$stackName}.{$pushName}' => true]);
                    \$__env->startPush('{$stackName}');
            ?>";
        });
        $this->app->get("cf-git.bkit")->extend('endOnce', function () {
            return "<?php
                    \$__env->stopPush();
                }
            ?>";
        });
    }
}
