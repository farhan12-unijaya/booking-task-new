<?php

namespace App\FilingModel;

use Illuminate\Database\Eloquent\Model;

class FormL extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'forml';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tenure_id',
    	'address_id',
        'branch_id',
        'branch_address_id',
        'applied_at',
        'resolved_at',
        'meeting_type_id',
        'is_editable',
        'filing_status_id',
        'created_by_user_id',
    ];

    public function references() {
        return $this->morphMany('App\FilingModel\Reference', 'filing');
    }

    public function tenure() {
        return $this->belongsTo('App\FilingModel\Tenure', 'tenure_id', 'id');
    }

    public function address() {
        return $this->belongsTo('App\OtherModel\Address', 'address_id', 'id');
    }

    public function branch() {
        return $this->belongsTo('App\FilingModel\Branch', 'branch_id', 'id');
    }

    public function branch_address() {
        return $this->belongsTo('App\OtherModel\Address', 'branch_address_id', 'id');
    }

    public function meeting_type() {
        return $this->belongsTo('App\MasterModel\MasterMeetingType', 'meeting_type_id', 'id');
    }

    public function status() {
        return $this->belongsTo('App\MasterModel\MasterFilingStatus', 'filing_status_id', 'id');
    }

    public function created_by() {
        return $this->belongsTo('App\User', 'created_by_user_id', 'id');
    }

    public function letters() {
        return $this->morphMany('App\OtherModel\Letter','filing');
    }

    public function distributions(){
        return $this->morphMany("App\FilingModel\Distribution","filing");
    }

    public function logs() {
        return $this->morphMany('App\LogModel\LogFiling', 'filing');
    }

    public function queries() {
        return $this->morphMany('App\FilingModel\Query', 'filing');
    }

    public function officers() {
        return $this->hasMany('App\FilingModel\FormLOfficer', 'forml_id', 'id');
    }

    public function formu() {
        return $this->morphOne('App\FilingModel\FormU', 'filing');
    }

    public function leaving() {
        return $this->hasMany('App\FilingModel\FormLLeaving', 'forml_id', 'id');
    }
}
