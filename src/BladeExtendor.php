<?php
/**
 * @author Shubin Sergei <is.captain.fail@gmail.com>
 * @license GNU General Public License v3.0
 * 08.03.2020 2020
 */

namespace CFGit\BKit;


use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Traits\Macroable;

class BladeExtendor
{
    use Macroable;
    protected $app;
    public function __construct(Application $app)
    {
        $this->app = $app;

        static::macro('extend', function ($name, $callback) {
            static::bootBladeDirective($name, $callback);
        });
    }

    const QUOTED_EXPRESSIONS = "/\s*((\'(.*)\')|(\\\"(.*)\\\"))\s*/mU";

    public static function bootBladeDirective($directiveName, $callback)
    {
        Blade::directive($directiveName, function ($expression) use ($callback) {
            preg_match_all(self::QUOTED_EXPRESSIONS, $expression, $matches, PREG_SET_ORDER);
            $buffer = [];
            foreach ($matches as $match) {
                $first = array_last($match);
                $hash = '~' . crc32($first);
                $buffer[$hash] = $first;
                $expression = str_replace(array_first($match), $hash, $expression);
            }
            $expression = array_map(function ($v) use ($buffer) {
                if (isset($v[0]) && ($v[0] === '~') && isset($buffer[$v])) {
                    $v = $buffer[$v];
                }
                return trim($v);
            }, explode(',', $expression));
            return call_user_func_array(
                $callback,
                $expression
            );
        });
    }

    public function extendBladeDirectives()
    {
        static::bootBladeDirective('pushOnce', function ($stackName, $pushName) {
            return "<?php
                if (is_null(config('__stacks__.{$stackName}.{$pushName}'))) {
                    config(['__stacks__.{$stackName}.{$pushName}' => true]);
                    \$__env->startPush('{$stackName}');
            ?>";
        });
        static::bootBladeDirective('endOnce', function () {
            return "<?php
                    \$__env->stopPush();
                }
            ?>";
        });
    }
}
