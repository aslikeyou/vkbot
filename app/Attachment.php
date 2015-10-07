<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use InsertUpdateTrait;

    public $incrementing = false;
    public $timestamps = false;
}
