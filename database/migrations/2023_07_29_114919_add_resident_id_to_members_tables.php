<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResidentIdToMembersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('household_head_members', function (Blueprint $table) {
            $table->integer('resident_id')->nullable();
        });
        Schema::table('household_heads', function (Blueprint $table) {
            $table->integer('resident_id')->nullable();
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
            $table->dropColumn('resident_id');
        });
        Schema::table('household_heads', function (Blueprint $table) {
            $table->dropColumn('resident_id');
        });
    }
}
