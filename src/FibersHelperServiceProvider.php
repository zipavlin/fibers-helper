<?php

namespace Fibers\Helper;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class FibersHelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/fibers.php', 'fibers'
        );
        // register required service providers
        $this->app->register('Axdlee\Config\ConfigServiceProvider');
        $this->app->register('EddIriarte\Console\Providers\SelectServiceProvider');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // define root folder
        define('FIBERS_HELPER', __DIR__);

        // extends
        $this->extendCollection();
        $this->extendRelationship();

        // publish vendor files
        $this->publishVendorFiles();
    }

    // publishing helpers
    private function publishVendorFiles()
    {
        // publish vendor files
        $this->publishes([
            __DIR__."/config/fibers.php" => config_path("fibers.php"),
        ], "config");
    }

    // extend helpers
    private function extendCollection()
    {
        // find and option in collection where item is set either 'label' or 'label:value'
        Collection::macro('option', function ($key) {
            if ($this->contains($key)) return true;
            if ($option = Arr::first(preg_grep("/$key:[\w|]+/", $this->toArray()))) {
                if (strpos($option, "|") !== false) return collect(explode("|", Str::after($option, "$key:")));
                return Str::after($option, "$key:");
            }
            return false;
        });
        // remove item from collection
        Collection::macro('delete', function ($item) {
            if ($this->contains($item)) return $this->filter(function ($i) use ($item) {
                return $i !== $item;
            })->whenEmpty(function () {
                return collect();
            });
            return $this;
        });
        // wrap items in quotes
        Collection::macro('quotes', function () {
            return $this->map(function ($item) {
                return "'$item'";
            });
        });
    }
    private function extendRelationship()
    {
        BelongsToMany::macro('getPivotColumns', function () {
            return $this->pivotColumns;
        });
        BelongsToMany::macro('getPivotTable', function () {
            return $this->resolveTableName($this->table);
        });
    }
}
