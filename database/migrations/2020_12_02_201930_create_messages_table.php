<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->nullable();
            $table->foreignId('chatroom_id');
            $table->string('text', 1000)->nullable();
            $table->text('image_path')->nullable();
            $table->text('file_path')->nullable();
            $table->string('file_name', 1000)->nullable();
            $table->boolean('read')->default(false);
            $table->timestamps();

            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            
            $table->foreign('chatroom_id')
                ->references('id')
                ->on('chatrooms')
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
        Schema::dropIfExists('messages');
    }
}
