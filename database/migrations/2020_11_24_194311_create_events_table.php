<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('host_id')->nullable();
            $table->foreignId('group_id');
            $table->string('description')->nullable();
            $table->dateTime('event_time');
            $table->dateTime('event_ending')->nullable();
            $table->string('event_place', 255);
            $table->timestamps();

            $table->foreign('host_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            
            $table->foreign('group_id')
                ->references('id')
                ->on('groups')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meetings');
    }
}
