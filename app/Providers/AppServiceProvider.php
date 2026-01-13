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
        // Установка русской локали для правильного отображения дат
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'ru_RU', 'rus');
        \Carbon\Carbon::setLocale('ru');
    }
}
