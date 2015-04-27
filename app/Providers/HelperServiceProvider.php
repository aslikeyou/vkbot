<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
/**
 * 
 * http://stackoverflow.com/questions/28290332/best-practices-for-custom-helpers-on-laravel-5
 * */
class HelperServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app_path().'/Helpers/*.php') as $filename){
            require_once($filename);
        }
    }
}