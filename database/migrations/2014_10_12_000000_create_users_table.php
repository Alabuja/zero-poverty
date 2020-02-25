<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('slug')->unique();
            $table->string('phone_number')->nullable();
            $table->string('mobile_number')->nullable();
            $table->text('date_of_birth')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->text('facebook_username')->nullable();
            $table->text('twitter_username')->nullable();
            $table->text('google_username')->nullable();
            $table->text('signature')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->text('about_me')->nullable();
            $table->string('image')->nullable();
            $table->date('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('package_type', ['Free', 'Affiliate']);
            $table->boolean('has_paid')->default(false);
            $table->boolean('isReferred')->default(false);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
