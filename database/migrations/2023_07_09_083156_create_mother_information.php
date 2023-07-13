<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotherInformation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mother_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId("household_head_member_id");
            $table->string('nhts')->nullable();
            $table->string('family_planning')->nullable();
            $table->string('maternal_care_services')->nullable();
            $table->string('give_birth_this_month')->default('no');
            $table->string('delivery')->nullable();
            $table->string('delivery_type')->nullable();
            $table->string('person_who_deliver')->nullable();
            $table->string('child_gender')->nullable();
            $table->string('birth_term')->nullable();
            $table->string('is_miscarriage_or_abortion')->default('no');
            $table->string('postpartum_with_newborn')->default('no');
            $table->string('postpartum_as_pregnant')->default('no');
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
        Schema::dropIfExists('mother_information');
    }
}
