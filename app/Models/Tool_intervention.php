<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Tool_intervention extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tool_interventions';
    protected $primaryKey = 'id';
    public $timestamps = true;
    // protected $guarded = ['id'];
    protected $fillable = ['description','tool_interventioncompanys_id','tool_interventiontypes_id','operator','date_inserted','date_intervention','tool_requests_id'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
        public function tool_interventioncompany()
        {
            return $this->belongsTo('App\Models\Tool_interventioncompany', 'tool_interventioncompanys_id');
        }

        public function tool_interventiontype()
        {
            return $this->belongsTo('App\Models\Tool_interventiontype', 'tool_interventiontypes_id');
        }
        public function tool_request()
        {
            return $this->belongsTo('App\Models\Tool_request', 'tool_requests_id');
        }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
