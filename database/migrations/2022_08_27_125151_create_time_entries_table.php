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
        Schema::create('time_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('external_id')->nullable()->index();
            $table->string('description');
            $table->string('project')->nullable();
            $table->string('client')->nullable();
            $table->timestamp('start');
            $table->timestamp('end')->nullable();
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
        Schema::dropIfExists('time_entries');
    }
};
