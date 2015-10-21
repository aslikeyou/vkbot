<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $name
 * @property string $screen_name
 * @property OtherGroup[] otherGroups
 *
 */
class MyGroup extends Model
{
    use InsertUpdateTrait;

    public $incrementing = false;
    public $timestamps = false;

    public function otherGroups() {
        return $this->belongsToMany(OtherGroup::class,(new WatchGroup())->getTable(),'my_group_id','other_group_id');
    }
}
