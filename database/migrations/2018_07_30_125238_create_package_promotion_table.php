<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagePromotionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_promotion', function (Blueprint $table) {
            $table->unsignedInteger('package_id')->nullable();
            $table->unsignedInteger('promotion_id')->nullable();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropForeign(['package_id']);
            $table->dropForeign(['region_id']);

            $table->dropColumn('package_id');
            $table->dropColumn('region_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->unsignedInteger('region_id')->nullable();
            $table->unsignedInteger('package_id')->nullable();

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });

        Schema::dropIfExists('package_promotion');
    }
}
