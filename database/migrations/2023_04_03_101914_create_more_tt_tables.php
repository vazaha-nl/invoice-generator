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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('toggl_id')->unique()->nullable();
            $table->foreignId('client_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null');
            $table->boolean('billable')->default(false);
            $table->double('rate', 8, 2)->nullable();

            $table->timestamps();
        });

        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropColumn('projectName');
            $table->dropColumn('clientName');
            $table->foreignId('project_id')
                ->nullable()
                ->after('description')
                ->constrained()
                ->onDelete('set null');
            $table->dropColumn('external_id');
            $table->unsignedBigInteger('toggl_id')
                ->unique()
                ->nullable()
                ->after('description');
            $table->dropColumn('started_at');
            $table->dropColumn('ended_at');
        });

        Schema::table('time_entries', function (Blueprint $table) {
            $table->timestamp('stopped_at')->nullable(true)->index()->after('project_id');
            $table->timestamp('started_at')->nullable(true)->index()->after('project_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_entries', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropForeign(['client_id']);
            $table->dropColumn('project_id');
            $table->dropColumn('client_id');
            $table->string('projectName')
                ->after('description');
            $table->string('clientName')
                ->after('description');
        });

        Schema::dropIfExists('clients');
        Schema::dropIfExists('projects');
    }
};
