<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
    public function boot()
    {
        \Illuminate\Pagination\Paginator::useTailwind();

        // Ensure system_settings table exists
        try {
            if (!Schema::hasTable('system_settings')) {
                Schema::create('system_settings', function ($table) {
                    $table->string('key')->primary();
                    $table->text('value')->nullable();
                    $table->string('description')->nullable();
                });

                DB::table('system_settings')->insert([
                    [
                        'key' => 'base_weight_limit',
                        'value' => '2.0',
                        'description' => 'Khối lượng cơ bản (kg) - dưới mức này không tính phí phụ trội'
                    ],
                    [
                        'key' => 'price_per_kg',
                        'value' => '5000',
                        'description' => 'Đơn giá mỗi kg phụ trội (đ/kg)'
                    ],
                ]);
            }
        } catch (\Exception $e) {
            // Silence exceptions during database setup / migrations
        }
    }
}
