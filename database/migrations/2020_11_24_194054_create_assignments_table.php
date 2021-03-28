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
            $table->foreignId('author_id')->nullable();
            $table->foreignId('group_id');
            $table->boolean('on_time'); 
            //assignment is etiher due some date (on_time = false) or must be done on time (true; on time = due time) 
            $table->dateTime('due');
            $table->string('duration')->nullable();
            $table->text('description');
            $table->integer('max_assignees')->nullable();
            $table->boolean('done')->default(false);
            $table->timestamps();
            
            $table->foreign('author_id')
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
        Schema::dropIfExists('assignments');
    }
}
