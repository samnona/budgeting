<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appropriations', function (Blueprint $table) {
            $table->foreignId('object_expenditure_id')->constrained('object_expenditures')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appropriations', function (Blueprint $table) {
            $table->dropForeign('object_expenditure_id');
        });
    }
};
