<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChildInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('child_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId("household_head_member_id");
            $table->string('fic')->default('Yes');
            $table->string('cic')->default('No');
            $table->string('vaccine_given')->default('No Vaccine');
            $table->string('exclusive_bf')->default('Yes');
            $table->string('dewormed')->default('No');
            $table->string('given_vitamin_sipplementation')->default('No');
            $table->string('nutritional_status')->default('Stunted');
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
        Schema::dropIfExists('child_information');
    }
}