<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersAndGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->text('avatar')->nullable();
            $table->text('banner')->nullable();
            $table->string('bio', 300)->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('got_assignment_notify')->default(true);
            $table->boolean('my_assignment_updated_notify')->default(true);
            $table->boolean('joined_event_updated_notify')->default(false);
            $table->boolean('created_by_me_assignment_updated_notify')->default(false);
            $table->boolean('created_by_me_event_updated_notify')->default(false);
            $table->rememberToken();
            $table->foreignId('active_group')->nullable();
            $table->timestamps();

            $table->foreign('active_group')
            ->references('id')
            ->on('groups');
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('avatar')->nullable();
            $table->foreignId('admin_id');
            $table->string('board', 500)->nullable();
            $table->timestamps();

            $table->foreign('admin_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('groups');
    }
}
