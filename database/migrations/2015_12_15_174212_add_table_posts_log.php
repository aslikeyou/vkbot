<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTablePostsLog extends Migration
{
    public $table = 'posts_log';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('my_group_name');
            $table->integer('my_group_id');
            $table->integer('my_group_post_id');
            $table->string('other_group_name');
            $table->integer('other_group_id');
            $table->integer('other_group_post_id');
            $table->text('post_json');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->table);
    }
}
