<?php

namespace App\Http\Controllers\Admin;

//use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\CrudModController;
use App\Models\Tool_position;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Tool_positionRequest as StoreRequest;
use App\Http\Requests\Tool_positionRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class Tool_positionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class Tool_positionCrudController extends CrudModController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        static::$controllernew = new Tool_positionCrudController();

        $this->crud->setModel('App\Models\Tool_position');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tool_position');
        $this->crud->setEntityNameStrings('tool position', 'tool positions');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in Tool_positionRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
        
        $this->crud->removeColumns(['active','date_start','date_end','tools_id','tool_buildings_id','tool_floors_id','tool_departments_id']);
        $this->crud->addColumn([
            'name' => 'active',
            'label'=> 'Active',
            'type' => 'check',
        ]);        
        $this->crud->addColumn([
            'name' => 'tools_id',
            'label' => 'Tool',
            'type' => 'select',
            'entity' => 'tool',
            'attribute' => 'name',
            'model' => "App\Models\Tool",
        ]);
        $this->crud->addColumn([
            'name' => 'tool_buildings_id',
            'label' => 'Tool building',
            'type' => 'select',
            'entity' => 'tool_building',
            'attribute' => 'name',
            'model' => "App\Models\Tool_building",
        ]);
        $this->crud->addColumn([
            'name' => 'tool_floors_id',
            'label' => 'Tool floor',
            'type' => 'select',
            'entity' => 'tool_floor',
            'attribute' => 'name',
            'model' => "App\Models\Tool_floor",
        ]);
        $this->crud->addColumn([
            'name' => 'tool_departments_id',
            'label' => 'Tool department',
            'type' => 'select',
            'entity' => 'tool_department',
            'attribute' => 'name',
            'model' => "App\Models\Tool_department",
        ]);       
        $this->crud->orderColumns(['tools_id','tool_buildings_id','tool_floors_id','tool_departments_id','date_start','date_end','active']);

        $this->crud->removeFields(['active','date_start','date_end','tools_id','tool_buildings_id','tool_floors_id','tool_departments_id'],'both');
        $this->crud->addField([
            'name' => 'active',
            'label'=> 'Active',
            'type' => 'checkbox',            
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'date_start',
            'label'=> 'Date start',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => true,
                'format' => 'yyyy-mm-dd',
                'language' => 'it'
            ],
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'date_end',
            'label'=> 'Date end',
            'type' => 'date_picker',
            'date_picker_options' => [
                'todayBtn' => true,
                'format' => 'yyyy-mm-dd',
                'language' => 'it'
            ],
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tools_id',
            'label' => 'Tool',
            'type' => 'select3_condition',
            'entity' => 'Tool',
            'attribute' => 'FullNome',
            'model' => "App\Models\Tool",
            'function' => 'getSelectToolAvailableActive'
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tool_buildings_id',
            'label' => 'Tool building',
            'type' => 'hidden',
            'attributes' => [
                'id' => 'input_tool_buildings_id'
            ]
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tool_floors_id',
            'label' => 'Tool floor',
            'type' => 'hidden',
            'attributes' => [
                'id' => 'input_tool_floors_id'
            ]
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tool_departments_id',
            'label' => 'Tool department',
            'type' => 'hidden',
            'attributes' => [
                'id' => 'input_tool_departments_id'
            ]            
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tool_position',
            'label' => 'Tool position',
            'type' => 'select3_dependency',
            'field_unique_name' => 'tool_position',
            'entity_next' => 'tool_building',
            'subfields' => [
                'tool_building' => [
                    'name' => 'tool_buildings_id',
                    'label' => 'Building',
                    'entity' => 'tool_building',
                    'entity_next' => 'tool_floor',
                    'attribute' => 'name',
                    'model' => 'App\Models\Tool_building',
                    'pivot' => false,
                    'number_columns' => 1,
                ],
                'tool_floor' => [
                    'name'           => 'tool_floors_id',
                    'label'          => 'Floor',
                    'entity'         => 'tool_floor',
                    'entity_previous' => 'tool_building',
                    'entity_next'    => 'tool_department',
                    'attribute'      => 'name',
                    'model'          => 'App\Models\Tool_floor',
                    'pivot'          => false,
                    'number_columns' => 1,
                ],
                'tool_department' => [
                    'name'           => 'tool_departments_id',
                    'label'          => 'Department',
                    'entity'         => 'tool_department',
                    'entity_previous' => 'tool_floor',
                    'attribute'      => 'name',
                    'model'          => 'App\Models\Tool_department',
                    'pivot'          => false,
                    'number_columns' => 1,
                ]
            ],
        ], 'update/create/both');
        $this->crud->orderFields(['active','date_start','date_end','tools_id','tool_buildings_id','tool_floors_id','tool_departments_id'],'both');

        $this->crud->allowAccess(['create', 'update', 'delete', 'sub_create', 'sub_update', 'sub_delete', 'list', 'search', 'show']);
        $this->crud->denyAccess([]);
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
                    'name' => 'tool position',
                    'name_route' => 'tool_position',
                    'controller' => $this,
                ],                
            ],             
            'sub_controller_new' => [

            ],
        ];

        $this->crud->sub_entry_ = [
            'crud' => [
                'name' => $controller_new['crud_controller_new'][0]['name'],
                'name_route' => $controller_new['crud_controller_new'][0]['name_route'],
                'controller_new' => $controller_new['crud_controller_new'][0]['controller'],
                'relation_id' => $entry_->getAttributes()['id'],
                'fields' => [
                    'show' => ['tools_id','tool_buildings_id','tool_floors_id','tool_departments_id','date_start','date_end','active']
                ],
                'show_relations' => [
                    'tool' => [            //edit
                        'columns' => ['FullNome'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->tool()    //edit
                    ],
                    'tool_building' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->tool_building()    //edit
                    ],
                    'tool_floor' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->tool_floor()    //edit
                    ],
                    'tool_department' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->tool_department()    //edit
                    ]     
                ],
                'search_mod' => true,
            ],
            'sub_crud_relations' => [
            ],
        ];

        return parent::show_();
    }

    public function store(StoreRequest $request)
    {
        $this->check_permissions('create');
        // your additional operations before save here
        $check = Tool_position::CheckPosition($request->request->get('tools_id'));

        if($check!=null && $request->request->get('active')==1 )
        {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors(['tools_id' => trans("Tool already used")]);
        }
        
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function store2(StoreRequest $request)
    {
        $this->check_permissions('sub_create');
        // your additional operations before save here

        //$reparto_id = Tool_position::getReparto($request->request->get('laboratorios_id'));
        //$piano_id = Tool_position::getPiano($reparto_id);
        $piano_id = Tool_position::getFloor($request->request->get('tool_departments_id'));
        $edificio_id = Tool_position::getBuilding($piano_id);

        //$request->request->set('repartos_id', $reparto_id);
        $request->request->set('tool_floors_id', $piano_id);
        $request->request->set('tool_buildings_id', $edificio_id);

        $check = Tool_position::CheckPosition($request->request->get('tools_id'));

        if($check!=null && $request->request->get('active')==1 )
            return response()->json(['message' => 'The given data was invalid.', 'errors' => [
                    'active' => ["Tool already used"]
                ]
            ], 422);

        return parent::store2_($request);      
    }

    public function update(UpdateRequest $request)
    {
        $users_id = $request->request->get('users_id') !== null ? $request->request->get('users_id') : null;
        $this->check_permissions('update', $users_id);
        // your additional operations before save here
        $check = Tool_position::CheckPosition($request->request->get('tools_id'));

        if($check!=null && $request->request->get('active')==1 )
        {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors(['tools_id' => trans("Tool already used")]);
        }

        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update2(UpdateRequest $request)
    {
        $users_id = $request->request->get('users_id') !== null ? $request->request->get('users_id') : null;
        $this->check_permissions('sub_update', $users_id);;

        $piano_id = Tool_position::getFloor($request->request->get('tool_departments_id'));
        $edificio_id = Tool_position::getBuilding($piano_id);

        $request->request->set('tool_floors_id', $piano_id);
        $request->request->set('tool_buildings_id', $edificio_id);
        
        $check = Tool_position::CheckPosition($request->request->get('tools_id'));

        if($check!=null && $request->request->get('active')==1 && $request->request->get('id')!==strval($check->id))
            return response()->json(['message' => 'The given data was invalid.', 'errors' => [
                    'active' => ["Tool already used"]
                ]
            ], 422);

        return parent::update2_($request);
    }

    public function destroy($id)
    {
        $model = Tool_position::find($id);
        if($model['users_id'] !== null)
            $users_id = $model['users_id'];
        else
            $users_id = null;

        $this->check_permissions('delete', $users_id);

        return parent::destroy_($id, $users_id);
    }
}
