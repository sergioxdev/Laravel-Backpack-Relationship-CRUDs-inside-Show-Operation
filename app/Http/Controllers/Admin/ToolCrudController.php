<?php

namespace App\Http\Controllers\Admin;

//use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\CrudModController;
use App\Models\Tool;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\ToolRequest as StoreRequest;
use App\Http\Requests\ToolRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class ToolCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ToolCrudController extends CrudModController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        static::$controllernew = new ToolCrudController();

        $this->crud->setModel('App\Models\Tool');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tool');
        $this->crud->setEntityNameStrings('tool', 'tools');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in ToolRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
                        
        $this->crud->removeColumns(['description','serial_number','num_inv_general','tool_types_id','active']);
        $this->crud->addColumn([
            'name' => 'active',
            'label'=> 'Active',
            'type' => 'check',
        ]);  
        $this->crud->addColumn([
            'name' => 'tool_types_id',
            'label' => 'Tool type',
            'type' => 'select',            
            'entity' => 'tool_type',
            'attribute' => 'name',
            'model' => "App\Models\Tool_type",
        ]);              
        $this->crud->orderColumns(['name','brand','model','description','serial_number','num_inv_general','tool_types_id','active']);
        

        $this->crud->removeFields(['description','tool_types_id','active'],'both');
        $this->crud->addField([
            'name' => 'description',
            'label' => 'Description',
            'type' => 'wysiwyg',
            'placeholder' => 'Description',
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'active',
            'label'=> 'Active',
            'type' => 'checkbox',            
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tool_types_id',
            'label' => 'Tool type',
            'type' => 'select',            
            'entity' => 'tool_type',
            'attribute' => 'name',
            'model' => "App\Models\Tool_type",
        ], 'update/create/both');
        $this->crud->orderFields(['name','brand','model','description','serial_number','num_inv_general','tool_types_id','active'],'both');

        $this->crud->allowAccess(['create', 'update', 'delete', 'list', 'search', 'show']);
        $this->crud->denyAccess(['sub_create', 'sub_update', 'sub_delete']);
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
                    'name' => 'tool',
                    'name_route' => 'tool',
                    'controller' => $this,
                ],                
            ],             
            'sub_controller_new' => [
                '0' => [
                    'name' => 'tool position',
                    'name_route' => 'tool_position',
                    'controller' => new Tool_positionCrudController(),
                ],
                '1' => [
                    'name' => 'tool note',
                    'name_route' => 'tool_note',
                    'controller' => new Tool_noteCrudController(),
                ],
                '2' => [
                    'name' => 'tool request',
                    'name_route' => 'tool_request',
                    'controller' => new Tool_requestCrudController(),
                ],
                '3' => [
                    'name' => 'tool calibration',
                    'name_route' => 'tool_calibration',
                    'controller' => new Tool_calibrationCrudController(),
                ],                
                '4' => [
                    'name' => 'tool manual',
                    'name_route' => 'tool_manual',
                    'controller' => new Tool_manualCrudController(),
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
                    'show' => ['name','brand','model','description','serial_number','num_inv_general','tool_types_id','active']
                ],
                'show_relations' => [
                    'tool_type' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->tool_type()    //edit
                    ],     
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
                    'relation_type' => 'hasMany',     			//edit
                    'relation_name' => 'tool_position',         //edit
                    'relation_id' =>  $entry_->getAttributes()['id'],
                    'fields' => [
                        'list' => ['tool_departments_id','active'],   //edit
                        'list_fake' => [],
                        'create' => ['active','date_start','date_end','tool_buildings_id','tool_floors_id','tool_departments_id'], //edit
                        'create_back' => [],
                        'create_default' => [],
                        'create_fake' => [],
                        'create_exclusion' => [],
                        'create_hidden' => [],
                        'update' => ['active','date_start','date_end','tool_buildings_id','tool_floors_id','tool_departments_id'], //edit
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
                ],
                $controller_new['sub_controller_new'][1]['name_route'] => [
                    'name' => $controller_new['sub_controller_new'][1]['name'],
                    'name_route' => $controller_new['sub_controller_new'][1]['name_route'],   
                    'controller_new' => $controller_new['sub_controller_new'][1]['controller'],
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)                       
                    'relation_type' => 'hasMany',     //edit
                    'relation_name' => 'tool_note',         //edit
                    'relation_id' =>  $entry_->getAttributes()['id'],
                    'fields' => [
                        'list' => ['note','date_inserted'],   //edit
                        'list_fake' => [],
                        'create' => ['note'], //edit
                        'create_back' => [],
                        'create_default' => [],
                        'create_fake' => [],
                        'create_exclusion' => [],
                        'create_hidden' => [],
                        'update' => ['note'], //edit
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
                ],

                $controller_new['sub_controller_new'][2]['name_route'] => [
                    'name' => $controller_new['sub_controller_new'][2]['name'],
                    'name_route' => $controller_new['sub_controller_new'][2]['name_route'],   
                    'controller_new' => $controller_new['sub_controller_new'][2]['controller'],
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)                       
                    'relation_type' => 'hasMany',     //edit
                    'relation_name' => 'tool_request',         //edit
                    'relation_id' =>  $entry_->getAttributes()['id'],
                    'fields' => [
                        'list' => ['tool_requesttypes_id','tool_requestprioritys_id','tool_requeststatuss_id','date_request','processed'],   //edit
                        'list_fake' => [],
                        'create' => ['description','activity_required','tool_requesttypes_id','tool_requestprioritys_id','date_request'], //edit
                        'create_back' => [],
                        'create_default' => [],
                        'create_fake' => [],
                        'create_exclusion' => [],
                        'create_hidden' => [],
                        'update' => ['description','activity_required','tool_requesttypes_id','tool_requestprioritys_id','tool_requeststatuss_id','date_request','processed'], //edit
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
                        'tool_requesttypes_id' => [
                            'type' => 'select3_color',
                            'uicolor' => 'color'
                        ],
                        'tool_requestprioritys_id' => [
                            'type' => 'select3_color',
                            'uicolor' => 'color'
                        ],
                        'tool_requeststatuss_id' => [
                            'type' => 'select3_color',
                            'uicolor' => 'color'
                        ],
                    ],
                    'enable_reorder' => [
                        //'attribute_name' => 'name',
                        //'depth' => '1'
                    ]
                ],

                $controller_new['sub_controller_new'][3]['name_route'] => [
                    'name' => $controller_new['sub_controller_new'][3]['name'],
                    'name_route' => $controller_new['sub_controller_new'][3]['name_route'],   
                    'controller_new' => $controller_new['sub_controller_new'][3]['controller'],
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)                       
                    'relation_type' => 'hasMany',     //edit
                    'relation_name' => 'tool_calibration',         //edit
                    'relation_id' =>  $entry_->getAttributes()['id'],
                    'fields' => [
                        'list' => ['date_calibration','date_deadline'],   //edit
                        'list_fake' => [],
                        'create' => ['date_calibration','date_deadline'], //edit
                        'create_back' => [],
                        'create_default' => [],
                        'create_fake' => [],
                        'create_exclusion' => [],
                        'create_hidden' => [],
                        'update' => ['date_calibration','date_deadline'], //edit
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
                ],

                $controller_new['sub_controller_new'][4]['name_route'] => [
                    'name' => $controller_new['sub_controller_new'][4]['name'],
                    'name_route' => $controller_new['sub_controller_new'][4]['name_route'],   
                    'controller_new' => $controller_new['sub_controller_new'][4]['controller'],
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)                       
                    'relation_type' => 'hasMany',     //edit
                    'relation_name' => 'tool_manual',         //edit
                    'relation_id' =>  $entry_->getAttributes()['id'],
                    'fields' => [
                        'list' => ['name'],   //edit
                        'list_fake' => [],
                        'create' => ['name','file'], //edit
                        'create_back' => [],
                        'create_default' => [],
                        'create_fake' => [],
                        'create_exclusion' => [],
                        'create_hidden' => [],
                        'update' => ['name','file'], //edit
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
        $model = Tool::find($id);
        if($model['users_id'] !== null)
            $users_id = $model['users_id'];
        else
            $users_id = null;
        
        $this->check_permissions('delete', $users_id);

        return parent::destroy_($id, $users_id);
    }
}
