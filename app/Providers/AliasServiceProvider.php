<?php

namespace App\Providers;

use App\Helper\StandardData;
use Hashids\Hashids;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class AliasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //

        $loader =  AliasLoader::getInstance();
        $loader->alias('SD',StandardData::class);
        // $loader->alias('Hashids',Hashids::class);
        
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
