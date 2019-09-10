<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('package_id');
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('min_order')->nullable()->comment('minimal total order');
            $table->boolean('percent')->nullable();
            $table->integer('value')->default(0);
            $table->string('day')->nullable()->comment('Sort day name. (Sun, Mon, Tue, Wed, Thu, Fri, Sat)');
            $table->string('time')->comment('JSON start time and end time. [start_time, end_time] / [start_date, end_date]');
            $table->string('target')->nullable();
            $table->boolean('active')->default(1);
            $table->text('description')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('promotion_id')->nullable();
            $table->string('code')->unique();
            $table->integer('discount')->default(0)->comment('Currency value');
            $table->string('payment')->default('Cash');
            $table->string('status')->default('Unpaid');
            $table->datetime('paid_date')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('package_id');
            $table->unsignedInteger('officer_id')->nullable();
            $table->string('code');
            $table->datetime('date');
            $table->text('location')->nullable();
            $table->text('note')->nullable();
            $table->text('detail')->nullable()->comment('JSON extra data. { "gender": "Men", "pet": "Yes" }');
            $table->string('status')->default('Pending');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('cascade');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('item_id');
            $table->integer('price');
            $table->integer('quantity');
            $table->string('package');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('promotions');
    }
}
