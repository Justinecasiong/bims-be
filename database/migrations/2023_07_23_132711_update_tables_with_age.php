<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTablesWithAge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('household_head_members', function (Blueprint $table) {
            $table->string('age')->nullable()->change();
        });
        Schema::table('household_heads', function (Blueprint $table) {
            $table->string('age')->nullable()->change();
        });
        Schema::table('residents', function (Blueprint $table) {
            $table->string('age')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('household_head_members', function (Blueprint $table) {
            $table->string('age')->change();
        });
        Schema::table('household_heads', function (Blueprint $table) {
            $table->string('age')->change();
        });
        Schema::table('residents', function (Blueprint $table) {
            $table->string('age')->change();
        });
    }
}
