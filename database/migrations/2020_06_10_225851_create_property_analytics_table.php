<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyAnalyticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_analytics', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedInteger("property_id");
            $table->unsignedInteger("analytic_type_id");
            $table->text('value');
            $table->index(['property_id'],"property_analytics_by_property_id_idx");
            $table->index(['analytic_type_id'],"property_analytics_by_Analytic_type_id_idx");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('property_analytics', function (Blueprint $table) {
            $table->dropIndex('property_analytics_by_property_id_idx');
            $table->dropIndex('property_analytics_by_Analytic_type_id_idx');
        });
        Schema::dropIfExists('property_analytics');
    }
}
