<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupWhiteboardPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_whiteboard_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id');
            $table->foreignId('group_id');
            $table->string('text', 2000)->nullable();
            $table->text('image_path')->nullable();
            $table->text('file_path')->nullable();
            $table->string('file_name', 1000)->nullable();
            $table->timestamps();

            $table->foreign('sender_id')
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
        Schema::dropIfExists('group_whiteboard_posts');
    }
}
