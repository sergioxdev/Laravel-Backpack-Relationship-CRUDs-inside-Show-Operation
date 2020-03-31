<?php

namespace App\Http\Controllers\Admin;

//use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\CrudModController;
use App\Models\Tool_floor;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Tool_floorRequest as StoreRequest;
use App\Http\Requests\Tool_floorRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class Tool_floorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class Tool_floorCrudController extends CrudModController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        static::$controllernew = new Tool_floorCrudController();

        $this->crud->setModel('App\Models\Tool_floor');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tool_floor');
        $this->crud->setEntityNameStrings('tool floor', 'tool floors');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in Tool_floorRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
        
        $this->crud->removeColumns(['tool_buildings_id']);
        $this->crud->addColumn([
            'name' => 'tool_buildings_id',
            'label' => 'Building',
            'type' => 'select',
            'entity' => 'tool_building',
            'attribute' => 'name',
            'model' => "App\Models\Tool_building",
        ]);
        $this->crud->orderColumns(['name','tool_buildings_id']);
        
        $this->crud->removeFields(['tool_buildings_id'],'both');
        $this->crud->addField([
            'name' => 'tool_buildings_id',
            'label' => 'Building',
            'type' => 'select',
            'entity' => 'tool_building',
            'attribute' => 'name',
            'model' => "App\Models\Tool_building",
        ], 'update/create/both');
        $this->crud->orderFields(['name','tool_buildings_id'],'both');

        $this->crud->allowAccess(['sub_create', 'sub_update', 'sub_delete', 'list', 'search', 'show']);
        $this->crud->denyAccess(['create', 'update', 'delete']);
        $this->crud->setShowView('admin/show_');
        $this->crud->enableAjaxTable();
        $this->check_permissions('list');
    }

    public function show($id)
    {
        $attributes = $this->crud->getEntry($id)->getAttributes();
        $users_id = array_key_exists('users_id', $attributes) ? $attributes['users_id'] : null;
        $this->check_permissions('show', $users_id);

        // get the info for that entry
        $this->data['entry'] = $this->crud->getEntry($id);
        $this->data['crud'] = $this->crud;
        $this->data['title'] = trans('backpack::crud.preview').' '.$this->crud->entity_name;
        
        $entry_ = $this->data['entry'];
        $controller_new = [
            'crud_controller_new' => [
                '0' => [
                    'name' => 'tool floor',
                    'name_route' => 'tool_floor',
                    'controller' => $this,
                ],                
            ],             
            'sub_controller_new' => [
                '0' => [
                    'name' => 'tool department',
                    'name_route' => 'tool_department',
                    'controller' => new Tool_departmentCrudController(),
                ]
            ],
        ];

        $this->crud->sub_entry_ = [
            'crud' => [
                'name' => $controller_new['crud_controller_new'][0]['name'],
                'name_route' => $controller_new['crud_controller_new'][0]['name_route'],
                'controller_new' => $controller_new['crud_controller_new'][0]['controller'],
                'relation_id' => $entry_->getAttributes()['id'],
                'fields' => [
                    'show' => ['name','tool_buildings_id']
                ],
                'show_relations' => [
                    'tool_building' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->tool_building()    //edit
                    ]    
                ],
                'search_mod' => true,
            ],
            'sub_crud_relations' => [
                $controller_new['sub_controller_new'][0]['name_route'] => [
                    'name' => $controller_new['sub_controller_new'][0]['name'],
                    'name_route' => $controller_new['sub_controller_new'][0]['name_route'],   
                    'controller_new' => $controller_new['sub_controller_new'][0]['controller'],
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)                       
                    'relation_type' => 'hasMany',     //edit
                    'relation_name' => 'tool_department',         //edit
                    'relation_id' =>  $entry_->getAttributes()['id'],
                    'fields' => [
                        'list' => ['name'],   //edit
                        'list_fake' => [],
                        'create' => ['name'], //edit
                        'create_back' => [],
                        'create_default' => [],
                        'create_fake' => [],
                        'create_exclusion' => [],
                        'create_hidden' => [],
                        'update' => ['name'], //edit
                        'update_back' => [],
                        'update_default' => [],
                        'update_fake' => [],
                        'update_exclusion' => [],
                        'update_hidden' => []
                    ],
                    'fields_pivot' => [
                        //'fieldEx' => [
                        //    'name' => "field_ex",     //edit
                        //    'label' => "Field Ex",    //edit //Table column heading
                        //    'function_name' => 'getFieldEx', //edit //the method in your Models //edit
                        //    'key' => $entry_->getAttributes()['id'],
                        //    'action' => ['list', 'create', 'update'],   //edit
                        //],
                    ],
                    'fields_mod' => [

                    ],
                     'show_relations' => [
                        //'fields_id' => [
                        //    'type' => 'select_link', 
                        //    'link' => 'true'
                        //],
                    ],
                    'enable_reorder' => [
                        //'attribute_name' => 'name',
                        //'depth' => '1'
                    ]
                ]
            ],
        ];

        return parent::show_();

    }

    public function store(StoreRequest $request)
    {
        $this->check_permissions('create');
        // your additional operations before save here
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function store2(StoreRequest $request)
    {
        $this->check_permissions('sub_create');
        // your additional operations before save here
 
        return parent::store2_($request);      
    }

    public function update(UpdateRequest $request)
    {
        $users_id = $request->request->get('users_id') !== null ? $request->request->get('users_id') : null;
        $this->check_permissions('update', $users_id);
        // your additional operations before save here
        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update2(UpdateRequest $request)
    {
        $users_id = $request->request->get('users_id') !== null ? $request->request->get('users_id') : null;
        $this->check_permissions('sub_update', $users_id);
        // your additional operations before save here

        return parent::update2_($request);
    }

    public function destroy($id)
    {
        $model = Tool_floor::find($id);
        if($model['users_id'] !== null)
            $users_id = $model['users_id'];
        else
            $users_id = null;

        $this->check_permissions('delete', $users_id);

        return parent::destroy_($id, $users_id);
    }

    public function getTool_floors()
    {
        $id = \Request::instance()->input()['id'];
        $pianos = Tool_floor::where('tool_buildings_id','=',$id)->get(['id','name'])->toArray();
        if (is_array($pianos)) {
            $pianos = $pianos;
        } else {
            $pianos = ['NULL' => 'NULL'];
        }
        return response()->json(json_encode($pianos));
    }
}
