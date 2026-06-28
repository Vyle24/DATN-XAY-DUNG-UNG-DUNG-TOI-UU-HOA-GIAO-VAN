<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('shippers', function (Blueprint $table) {
            $table->string('region', 20)->default('10.7769,106.7009')->after('vehicle_type')->comment('Tọa độ trung tâm khu vực mặc định (VD: HCM)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shippers', function (Blueprint $table) {
            $table->dropColumn('region');
        });
    }
};
