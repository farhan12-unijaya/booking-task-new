<?php

namespace App\OtherModel;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'booking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function guestlist() {
        return $this->hasMany('App\OtherModel\Guestlist', 'booking_id', 'id');
    }

    public function room() {
        return $this->hasOne('App\MasterModel\MasterRoom','id','room_id');
    }
}
