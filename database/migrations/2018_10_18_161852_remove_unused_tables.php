<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnusedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('user_agent_level');
        Schema::dropIfExists('agent_level_service');
        Schema::dropIfExists('agent_levels');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('agent_levels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('agent_level_service', function (Blueprint $table) {
            $table->unsignedInteger('agent_level_id');
            $table->unsignedInteger('service_id');
            $table->boolean('percent')->default(true);
            $table->unsignedInteger('value')->default(0);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('agent_level_id')->references('id')->on('agent_levels')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });

        Schema::create('user_agent_level', function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('agent_level_id');
            $table->boolean('active')->default(true);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('agent_level_id')->references('id')->on('agent_levels')->onDelete('cascade');
        });
    }
}
