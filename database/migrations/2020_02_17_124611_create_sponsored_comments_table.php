<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSponsoredCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sponsored_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sponsored_post_id');
            $table->unsignedBigInteger('user_id');
            $table->longText('body');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sponsored_post_id')->references('id')->on('sponsored_posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sponsored_comments');
    }
}
