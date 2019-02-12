<?php

namespace App\OtherModel;

use Illuminate\Database\Eloquent\Model;

class Guestlist extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    public $timestamps = false;
    protected $table = 'guestlist';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function Booking() {
        return $this->belongsTo('App\OtherModel\Booking', 'booking_id');
    }
}
