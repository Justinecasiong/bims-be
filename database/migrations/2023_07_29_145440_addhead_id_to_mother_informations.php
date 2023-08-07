<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddheadIdToMotherInformations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mother_information', function (Blueprint $table) {
            $table->foreignId("household_head_member_id")->nullable()->change();
            $table->foreignId("household_head_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mother_information', function (Blueprint $table) {
            $table->foreignId("household_head_member_id")->change();
            $table->dropColumn("household_head_id")->change();
        });
    }
}
