<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHouseholdHeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('household_heads', function (Blueprint $table) {
            $table->id();
            $table->foreignId("zone_id");
            $table->string('household_num');
            $table->string('family_num');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->string('sex');
            $table->string('birthdate');
            $table->string('civil_status');
            $table->string('age');
            $table->string('relationship');
            $table->string('education');
            $table->string('occupation');
            $table->string('fp_method');
            $table->string('pwd_status');
            $table->string('senior_citizen');
            $table->string('oosy');
            $table->string('residency');
            $table->longText('address')->nullable();
            $table->longText('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('household_heads');
    }
}