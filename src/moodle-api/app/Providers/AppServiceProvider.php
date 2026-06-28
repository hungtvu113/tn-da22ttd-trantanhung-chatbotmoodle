<?php

namespace App\Providers;

use App\Services\DataSource\DatabaseSource;
use App\Services\DataSource\MoodleDataSourceInterface;
use App\Services\DataSource\WebServiceSource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Chọn nguồn dữ liệu Moodle theo cấu hình MOODLE_DATA_SOURCE (db | ws).
        $this->app->bind(MoodleDataSourceInterface::class, function ($app) {
            $source = config('services.moodle.data_source', 'db');

            return $source === 'ws'
                ? $app->make(WebServiceSource::class)
                : $app->make(DatabaseSource::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
