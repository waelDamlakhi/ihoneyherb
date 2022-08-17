<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        $lang = $request->header('lang');
        if ($lang == 'en') 
        {
            app()->setLocale('en');
        }
        else
        {
            app()->setLocale('ar');
        }
    }
}
