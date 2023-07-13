<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opt_records', function (Blueprint $table) {
            $table->id();
            $table->string('purok');
            $table->string('household_num');
            $table->string('name_of_caregiver');
            $table->string('preschooler_name');
            $table->string('sex');
            $table->string('birthdate');
            $table->string('weight');
            $table->string('length_height');
            $table->string('age_in_months');
            $table->string('weight_kg');
            $table->string('length_height_cm');
            $table->string('weight_for_age');
            $table->string('length_height_for_age');
            $table->string('weight_for_length_height');
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
        Schema::dropIfExists('opt_records');
    }
}