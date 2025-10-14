<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gate::policy(Menu::class, MenuPolicy::class);
        // Gate::policy(Category::class, CategoryPolicy::class);
        // Gate::policy(Table::class, TablePolicy::class);


        // DB::listen(function ($query) {
        //     \Log::info($query->sql, $query->bindings);
        // });
    }
}
