<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupRegionData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();
        });

        Schema::create('region_officer', function (Blueprint $table) {
            $table->unsignedInteger('region_id');
            $table->unsignedInteger('officer_id');

            $table->index(['region_id', 'officer_id']);

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('cascade');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->unsignedInteger('region_id')->nullable()
                ->after('id');

            $table->index(['region_id']);

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->unsignedInteger('region_id')->nullable()
                ->after('id');

            $table->index(['region_id']);

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->unsignedInteger('region_id')->nullable()
                ->after('id');

            $table->index(['region_id']);

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedInteger('region_id')->nullable()
                ->after('id');

            $table->index(['region_id']);

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('region_id')->nullable()
                ->after('id');

            $table->index(['region_id']);

            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['region_id']);

            $table->dropIndex(['region_id']);
            
            $table->dropColumn('region_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['region_id']);

            $table->dropIndex(['region_id']);
            
            $table->dropColumn('region_id');
        });

        Schema::table('promotions', function (Blueprint $table) {
            $table->dropForeign(['region_id']);

            $table->dropIndex(['region_id']);
            
            $table->dropColumn('region_id');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropForeign(['region_id']);

            $table->dropIndex(['region_id']);
            
            $table->dropColumn('region_id');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['region_id']);

            $table->dropIndex(['region_id']);
            
            $table->dropColumn('region_id');
        });

        Schema::dropIfExists('region_officer');

        Schema::dropIfExists('regions');
    }
}
