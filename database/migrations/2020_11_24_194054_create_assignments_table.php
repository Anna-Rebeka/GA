<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('responsible_id')->nullable();
            $table->foreignId('author_id');
            $table->foreignId('group_id');
            $table->dateTime('due');
            $table->text('description');
            $table->timestamps();

            $table->foreign('responsible_id')
                ->references('id')
                ->on('users');
            
            $table->foreign('author_id')
                ->references('id')
                ->on('users');
            
            $table->foreign('group_id')
                ->references('id')
                ->on('groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assignments');
    }
}