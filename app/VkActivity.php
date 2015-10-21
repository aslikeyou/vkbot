<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $source_id
 * @property string $post_id
 * @property string $created_at
 * @property string $updated_at
 */
class VkActivity extends Model {

	//
    protected $table = 'vk_activities';
    protected $fillable = ['post_id', 'source_id'];
}
