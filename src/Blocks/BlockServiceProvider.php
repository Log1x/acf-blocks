<?php

namespace App\Blocks;

use Roots\Acorn\ServiceProvider;
use function Roots\app;
use function Roots\config;

class BlockServiceProvider extends ServiceProvider
{
    /**
     * Register and compose blocks.
     *
     * @return void
     */
    public function register()
    {
        if (is_null(config('blocks')) && config('app.preflight')) {
            app('files')->copy(realpath(__DIR__ . '/../config/blocks.php'), app()->configPath('blocks.php'));
        }

        collect(config('blocks.blocks'))
            ->each(function ($block) {
                if (is_string($block)) {
                    $block = new $block($this);
                }

                $block->compose();
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
