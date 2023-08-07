<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResidencyEndTohouseHold extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('household_head_members', function (Blueprint $table) {
            $table->string('residency_end')->nullable();
        });
        Schema::table('household_heads', function (Blueprint $table) {
            $table->string('residency_end')->nullable();
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
            $table->dropColumn('residency_end');
        });
        Schema::table('household_heads', function (Blueprint $table) {
            $table->dropColumn('residency_end');
        });
    }
}
