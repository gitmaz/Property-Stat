<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnalyticTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analytic_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('units')->nullable();
            $table->boolean("is_numeric");
            $table->integer("num_decimal_places");
            $table->index(['name'],"property_analytic_types_by_name_idx");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analytic_types', function (Blueprint $table) {
            $table->dropIndex('property_analytic_types_by_name_idx');
        });
        Schema::dropIfExists('analytic_types');

    }
}
