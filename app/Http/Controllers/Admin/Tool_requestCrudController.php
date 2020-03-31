<?php

namespace App\Http\Controllers\Admin;

//use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\CrudModController;
use App\Models\Tool_request;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Tool_requestRequest as StoreRequest;
use App\Http\Requests\Tool_requestRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;
use Carbon\Carbon;

/**
 * Class Tool_requestCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class Tool_requestCrudController extends CrudModController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        static::$controllernew = new Tool_requestCrudController();

        $this->crud->setModel('App\Models\Tool_request');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tool_request');
        $this->crud->setEntityNameStrings('tool request', 'tool requests');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in Tool_requestRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');

        $this->crud->removeColumns(['description','activity_required','tools_id','tool_requesttypes_id','tool_requestprioritys_id','tool_requeststatuss_id','users_id','date_inserted','date_request','processed','date_processed']);
        $this->crud->addColumn([
            'name' => 'tools_id',
            'label' => 'Tool',
            'type' => 'select',
            'entity' => 'tool',
            'attribute' => 'name',
            'model' => "App\Models\Tool",
        ]);
        $this->crud->addColumn([
            'name' => 'tool_requesttypes_id',
            'label' => 'Request type',
            'type' => 'select3_color',
            'entity' => 'tool_requesttype',
            'attribute' => 'name',
            'model' => "App\Models\Tool_requesttype",
            'uicolor' => 'color'
        ]);
        $this->crud->addColumn([
            'name' => 'tool_requestprioritys_id',
            'label' => 'Request priority',
            'type' => 'select3_color',
            'entity' => 'tool_requestpriority',
            'attribute' => 'name',
            'model' => "App\Models\Tool_requestpriority",
            'uicolor' => 'color'
        ]);
        $this->crud->addColumn([
            'name' => 'tool_requeststatuss_id',
            'label' => 'Request status',
            'type' => 'select3_color',
            'entity' => 'tool_requeststatus',
            'attribute' => 'name',
            'model' => "App\Models\Tool_requeststatus",
            'uicolor' => 'color'
        ]);
        $this->crud->addColumn([
            'name' => 'processed',
            'label'=> 'Processed',
            'type' => 'check',
        ]);
        $this->crud->orderColumns(['description','activity_required','tools_id','tool_requesttypes_id','tool_requestprioritys_id','tool_requeststatuss_id','users_id','date_inserted','date_request','processed','date_processed']);

        $this->crud->removeFields(['description','activity_required','tools_id','tool_requesttypes_id','tool_requestprioritys_id','tool_requeststatuss_id','users_id','date_inserted','date_request','processed','date_processed'],'both');
        $this->crud->addField([
            'name' => 'description',
            'label' => 'Description',
            'type' => 'wysiwyg',
            'placeholder' => 'Description',
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'activity_required',
            'label' => 'Activity required',
            'type' => 'wysiwyg',
            'placeholder' => 'Activity required',
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tools_id',
            'label' => 'Tool',
            'type' => 'select',
            'entity' => 'tool',
            'attribute' => 'name',
            'model' => "App\Models\Tool",
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tool_requesttypes_id',
            'label' => 'Request type',
            'type' => 'select',
            'entity' => 'tool_requesttype',
            'attribute' => 'name',
            'model' => "App\Models\Tool_requesttype",
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tool_requestprioritys_id',
            'label' => 'Request priority',
            'type' => 'select',
            'entity' => 'tool_requestpriority',
            'attribute' => 'name',
            'model' => "App\Models\Tool_requestpriority",
        ], 'update');
        $this->crud->addField([
            'name' => 'tool_requeststatuss_id',
            'label' => 'Request status',
            'type' => 'select',
            'entity' => 'tool_requeststatus',
            'attribute' => 'name',
            'model' => "App\Models\Tool_requeststatus",
        ], 'update');
        $this->crud->addField([
            'name' => 'date_request',
            'label'=> 'Date request',
            'type' => 'date_picker',            
            'date_picker_options' => [
                'todayBtn' => true,
                'format' => 'yyyy-mm-dd',
                'language' => 'it'
            ],
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'processed',
            'label'=> 'Processed',
            'type' => 'checkbox',
        ], 'update');
        $this->crud->orderFields(['description','activity_required','tools_id','tool_requesttypes_id','tool_requestprioritys_id','tool_requeststatuss_id','users_id','date_inserted','date_request','processed','date_processed'],'both');

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
                    'name' => 'tool request',
                    'name_route' => 'tool_request',
                    'controller' => $this,
                ],                
            ],             
            'sub_controller_new' => [
                '0' => [
                    'name' => 'tool request comment', 
                    'name_route' => 'tool_requestcomment',
                    'controller' => new Tool_requestcommentCrudController(),
                ],
                '1' => [
                    'name' => 'tool intervention',
                    'name_route' => 'tool_intervention',
                    'controller' => new Tool_interventionCrudController(),
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
                    'show' => ['tools_id','tool_requesttypes_id','tool_requestprioritys_id','tool_requeststatuss_id','users_id','description','activity_required','date_request','processed','date_processed']
                ],
                'column_check_permission' => ['processed'],
                'show_relations' => [
                    'tool' => [            //edit
                        'columns' => ['FullNome'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->tool()    //edit
                    ],
                    'tool_requesttype' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'entry' => $entry_->tool_requesttype()    //edit
                    ],
                    'tool_requestpriority' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'entry' => $entry_->tool_requestpriority()    //edit
                    ],
                    'tool_requeststatus' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'entry' => $entry_->tool_requeststatus()    //edit
                    ],
                    'user' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->user()    //edit
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
                    'relation_name' => 'tool_requestcomment',         //edit
                    'relation_id' =>  $entry_->getAttributes()['id'],
                    'fields' => [
                        'list' => ['content','users_id'],   //edit
                        'list_fake' => [],
                        'create' => ['content'], //edit
                        'create_back' => [],
                        'create_default' => [],
                        'create_fake' => [],
                        'create_exclusion' => [],
                        'create_hidden' => [],
                        'update' => ['content'], //edit
                        'update_back' => [],
                        'update_default' => [],
                        'update_fake' => [],
                        'update_exclusion' => [],
                        'update_hidden' => [],
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
                    'relation_name' => 'tool_intervention',         //edit
                    'relation_id' =>  $entry_->getAttributes()['id'],
                    'fields' => [
                        'list' => ['tool_interventioncompanys_id','tool_interventiontypes_id','operator','date_intervention'],   //edit
                        'list_fake' => [],
                        'create' => ['tool_interventioncompanys_id','tool_interventiontypes_id','operator','date_intervention','description'], //edit
                        'create_back' => [],
                        'create_default' => [],
                        'create_fake' => [],
                        'create_exclusion' => [],
                        'create_hidden' => [],
                        'update' => ['tool_interventioncompanys_id','tool_interventiontypes_id','operator','date_intervention','description'], //edit
                        'update_back' => [],
                        'update_default' => [],
                        'update_fake' => [],
                        'update_exclusion' => [],
                        'update_hidden' => [],
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
        $tool_requesttypes_id = $request->request->get('tool_requesttypes_id');

        $request_priority_id = Tool_request::getRequestpriorityByRequesttypeId($tool_requesttypes_id);
        $request->request->set('tool_requestprioritys_id', $request_priority_id);

        $request_status_id = Tool_request::getRequeststatusIdFirst();
        $request->request->set('tool_requeststatuss_id', $request_status_id);

        $request->request->set('users_id', backpack_auth()->getUser()->id);
        $request->request->set('date_inserted', Carbon::now());
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function store2(StoreRequest $request)
    {
        $this->check_permissions('sub_create');
        // your additional operations before save here
        $tool_requesttypes_id = $request->request->get('tool_requesttypes_id');

        $request_priority_id = Tool_request::getRequestpriorityByRequesttypeId($tool_requesttypes_id);
        $request->request->set('tool_requestprioritys_id', $request_priority_id);

        $request_status_id = Tool_request::getRequeststatusIdFirst();
        $request->request->set('tool_requeststatuss_id', $request_status_id);

        $request->request->set('users_id', backpack_auth()->getUser()->id);
        $request->request->set('date_inserted', Carbon::now());        
        return parent::store2_($request);      
    }

    public function update(UpdateRequest $request)
    {
        $users_id = $request->request->get('users_id') !== null ? $request->request->get('users_id') : null;
        $this->check_permissions('update', $users_id);
        // your additional operations before save here
        $processed = $request->request->get('processed');

        if($processed)
        {
            $request->request->set('date_processed', Carbon::now());
        }
        else
        {
            $request->request->set('date_processed', NULL);

        }

        $redirect_location = parent::updateCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function update2(UpdateRequest $request)
    {
        $users_id = $request->request->get('users_id') !== null ? $request->request->get('users_id') : null;
        $this->check_permissions('sub_update', $users_id);

        $processed = $request->request->get('processed');

        if($processed)
        {
            $request->request->set('date_processed', Carbon::now());
        }
        else
        {
            $request->request->set('date_processed', NULL);

        }
        // your additional operations before save here 
        return parent::update2_($request);
    }

    public function destroy($id)
    {
        $model = Tool_request::find($id);
        if($model['users_id'] !== null)
            $users_id = $model['users_id'];
        else
            $users_id = null;

        $this->check_permissions('delete', $users_id);

        return parent::destroy_($id, $users_id);
    }
}
