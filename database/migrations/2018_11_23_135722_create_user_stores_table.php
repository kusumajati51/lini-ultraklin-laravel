<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('region_id')->nullable();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('owner');
            $table->string('identity_card_number');
            $table->string('identity_card')->nullable();
            $table->string('address');
            $table->float('lat', 10, 6)->nullable();
            $table->float('lng', 10, 6)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending');
            $table->boolean('active')->default(false);
            $table->string('actived_at')->nullable();
            $table->string('actived_by')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
        });

        Schema::create('store_images', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('store_id');
            $table->string('filename')->nullable();
            $table->boolean('primary')->default(false);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('user_stores')->onDelete('cascade');
        });

        Schema::create('store_package', function (Blueprint $table) {
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('package_id');

            $table->foreign('store_id')->references('id')->on('user_stores')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });

        Schema::create('store_order_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('order_id');
            $table->string('status');
            $table->timestamps();

            $table->foreign('store_id')->references('id')->on('user_stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('store_id')->nullable()
                ->after('package_id');

            $table->float('lat', 10, 6)->nullable()
                ->after('location');
            $table->float('lng', 10, 6)->nullable()
                ->after('lat');

            $table->foreign('store_id')->references('id')->on('user_stores')->onDelete('restrict');
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
            $table->dropForeign('orders_store_id_foreign');
            
            $table->dropColumn('store_id');
            $table->dropColumn('lat');
            $table->dropColumn('lng');
        });

        Schema::dropIfExists('store_order_histories');
        Schema::dropIfExists('store_package');
        Schema::dropIfExists('store_images');
        Schema::dropIfExists('user_stores');
    }
}
