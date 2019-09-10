<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeAndReferralToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('code', 10)->nullable()
                ->index()
                ->after('id');

            $table->string('referral', 10)->nullable()
                ->after('code');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('referral')->references('code')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referral']);

            $table->dropColumn([
                'code', 'referral'
            ]);
        });
    }
}
