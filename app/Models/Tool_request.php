<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Tool_request extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tool_requests';
    protected $primaryKey = 'id';
    public $timestamps = true;
    // protected $guarded = ['id'];
    protected $fillable = ['description','activity_required','tools_id','tool_requesttypes_id','tool_requestprioritys_id','tool_requeststatuss_id','users_id','date_inserted','date_request','processed','date_processed'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
        static public function getRequestpriorityByRequesttypeId($tipo_richiestas_id)
        {
            return Tool_requesttype::where('id', '=', $tipo_richiestas_id)->pluck('tool_requestprioritys_id')->toArray()[0];
        }
        
        static public function getRequeststatusIdFirst()
        {
            $richiesta_stato = Tool_requeststatus::first();
            return $richiesta_stato['id'];
        }

        public function getFullNomeAttribute()
        {
           return $this->tool['name'];
        }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
        public function tool()
        {
            return $this->belongsTo('App\Models\Tool', 'tools_id');
        }

        public function tool_requesttype()
        {
            return $this->belongsTo('App\Models\Tool_requesttype', 'tool_requesttypes_id');
        }

        public function tool_requestpriority()
        {
            return $this->belongsTo('App\Models\Tool_requestpriority', 'tool_requestprioritys_id');
        }

        public function tool_requeststatus()
        {
            return $this->belongsTo('App\Models\Tool_requeststatus', 'tool_requeststatuss_id');
        }

        public function user()
        {
            return $this->belongsTo('App\User', 'users_id');
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
