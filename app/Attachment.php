<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property integer $post_id
 * @property integer $date
 * @property string $type
 * @property string $photo_75
 * @property string $photo_130
 * @property string $photo_604
 * @property string $hash
 */
class Attachment extends Model
{
    use InsertUpdateTrait;

    public $incrementing = false;
    public $timestamps = false;
}
