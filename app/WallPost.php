<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $date
 * @property string $post_type
 * @property string $text
 * @property string $post_source
 * @property integer $comments_count
 * @property integer $likes_count
 * @property integer $reposts_count
 */
class WallPost extends Model
{
    use InsertUpdateTrait;

    public $incrementing = false;
    public $timestamps = false;

    /**
     * Get the comments for the blog post.
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'post_id', 'id');
    }
}
