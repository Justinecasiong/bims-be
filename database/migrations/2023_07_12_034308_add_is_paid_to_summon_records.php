<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPaidToSummonRecords extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summon_records', function (Blueprint $table) {
            $table->boolean('is_paid')->nullable();
        });

        Schema::table('revenues', function (Blueprint $table) {
            $table->string("custom_recipient")->nullable();
            $table->foreignId("resident_id")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('summon_records', function (Blueprint $table) {
            $table->dropColumn('is_paid');
        });

        Schema::table('revenues', function (Blueprint $table) {
            $table->dropColumn('custom_recipient');
            $table->foreignId("resident_id")->change();
        });
    }
}
