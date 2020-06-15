<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            //$table->unsignedInteger('DestinationSourceSystemUUID');
            $table->uuid('guid');
            $table->timestamps();
            $table->string('suburb');
            $table->string('state');
            $table->string('country');
            $table->index(['suburb'],"properties_by_suburb_idx");
            $table->index(['state'],"properties_by_state_idx");
            $table->index(['country'],"properties_by_country_idx");



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex('properties_by_suburb_idx');
            $table->dropIndex('properties_by_state_idx');
            $table->dropIndex('properties_by_country_idx');
        });
        Schema::dropIfExists('properties');
    }
}
