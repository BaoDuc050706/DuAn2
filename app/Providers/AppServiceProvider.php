<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

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
        View::composer('*', function ($view) {
            try {
                $featuredCategories = Category::query()
                    ->orderBy('name')
                    ->take(8)
                    ->get(['name', 'slug']);
            } catch (\Throwable $e) {
                $featuredCategories = collect();
            }

            $view->with('featuredCategories', $featuredCategories);
        });
    }
}
