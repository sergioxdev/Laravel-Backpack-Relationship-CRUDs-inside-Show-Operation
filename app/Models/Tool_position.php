<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Tool_position extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'tool_positions';
    protected $primaryKey = 'id';
    public $timestamps = true;
    // protected $guarded = ['id'];
    protected $fillable = ['active','date_start','date_end','tools_id','tool_buildings_id','tool_floors_id','tool_departments_id'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
        /*
        static public function getReparto($laboratorio_id)
        {
            return Laboratorio::where('id', $laboratorio_id)->get()->first()->reparto->id;
        }
        */

        static public function getBuilding($piano_id)
        {
            return Tool_floor::where('id', $piano_id)->get()->first()->tool_building->id;
        }

        static public function getFloor($reparto_id)
        {
            return Tool_department::where('id', $reparto_id)->get()->first()->tool_floor->id;
        }

        static public function CheckPosition($id)
        {
            return Tool_position::where('tools_id', $id)
                ->where('active', '=', '1')
                ->get()->first();
        }

        static public function getPosizioneApparecchiaturaByIdApparecchiatura($apparecchiaturas_id)
        {
            return Posizione_apparecchiatura::where('apparecchiaturas_id', '=', $apparecchiaturas_id)->get()->first();
        }

        public function getFullNomeAttribute()
        {
           return $this->tool_department['name'];
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
        
        public function tool_building()
        {
            return $this->belongsTo('App\Models\Tool_building', 'tool_buildings_id');
        }
    
        public function tool_floor()
        {
            return $this->belongsTo('App\Models\Tool_floor', 'tool_floors_id');
        }
    
        public function tool_department()
        {
            return $this->belongsTo('App\Models\Tool_department', 'tool_departments_id');
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
