<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class VkActivity extends Model {

	//
    protected $table = 'vk_activities';
    protected $fillable = ['post_id', 'source_id'];
}
