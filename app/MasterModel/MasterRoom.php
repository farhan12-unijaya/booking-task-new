<?php

namespace App\MasterModel;

use Illuminate\Database\Eloquent\Model;

class MasterRoom extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'master_room';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function kategori() {
        return $this->belongsTo('App\MasterModel\MasterRoomCategory', 'master_room_category_id');
    }
}
