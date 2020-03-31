<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tools';
    protected $primaryKey = 'id';
    public $timestamps = true;
    // protected $guarded = ['id'];
    protected $fillable = ['name','brand','model','description','serial_number','num_inv_general','tool_types_id','active'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
        public function getFullNomeAttribute()
        {
            return $this->tool_type['name'].': '. $this->name.' - '.$this->brand.' - '.$this->model;
        }

        static public function getSelectToolAvailableActive($id)
        {
            return Tool::where('active', '=', '1')
            ->whereHas('tool_position', function ($query) {
                    $query->where('active', '=', '0');
            })->get();
            
            /*
            return Tool::where('id', '=', $id)
                ->orWhereHas('tool_position', function ($query) {
                    $query->where('active', '=', '0');
                })->get();
            */
        }

        static public function getSelectToolPositionActive()
        {
            return Tool::where('active', '=', '1')
            ->whereHas('tool_position', function ($query) {
                    $query->where('active', '=', '1');
                })->get();
        }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
        public function tool_type()
        {
            return $this->belongsTo('App\Models\Tool_type', 'tool_types_id');
        }
    
        public function tool_position()
        {
            return $this->hasMany('App\Models\Tool_position', 'tools_id');
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
