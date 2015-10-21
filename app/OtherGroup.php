<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $screen_name
 */
class OtherGroup extends Model
{
    public $incrementing = false;
    public $timestamps = false;
}
