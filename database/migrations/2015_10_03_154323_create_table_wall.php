<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wall_posts', function (Blueprint $table) {
            $table->integer('id');
            $table->primary('id');
            $table->integer('date');
            $table->string('post_type');
            $table->string('text');
            $table->string('post_source');
            $table->integer('comments_count');
            $table->integer('likes_count');
            $table->integer('reposts_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wall_posts');
    }
}
