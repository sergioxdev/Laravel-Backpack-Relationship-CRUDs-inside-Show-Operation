<?php

namespace App\Http\Controllers\Admin;

//use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\CrudModController;
use App\Models\Tool_type;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Tool_typeRequest as StoreRequest;
use App\Http\Requests\Tool_typeRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class Tool_typeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class Tool_typeCrudController extends CrudModController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        static::$controllernew = new Tool_typeCrudController();

        $this->crud->setModel('App\Models\Tool_type');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tool_type');
        $this->crud->setEntityNameStrings('tool type', 'tool types');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in Tool_typeRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');

        //$this->crud->removeColumns(['name']);
        $this->crud->orderColumns(['name']);
        
        //$this->crud->removeFields(['name'],'both');
        $this->crud->orderFields(['name'],'both');

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
                    'name' => 'tool type',
                    'name_route' => 'tool_type',
                    'controller' => $this,
                ],                
            ],             
            'sub_controller_new' => [
                '0' => [
                    'name' => 'tool',
                    'name_route' => 'tool',
                    'controller' => new ToolCrudController(),
                ],
            ],
        ];

        $this->crud->sub_entry_ = [
            'crud' => [
                'name' => $controller_new['crud_controller_new'][0]['name'],
                'name_route' => $controller_new['crud_controller_new'][0]['name_route'],
                'controller_new' => $controller_new['crud_controller_new'][0]['controller'],
                'relation_id' => $entry_->getAttributes()['id'],
                'fields' => [
                    'show' => ['name']
                ],
                'show_relations' => [    
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
                    'relation_name' => 'tool',         //edit
                    'relation_id' =>  $entry_->getAttributes()['id'],
                    'fields' => [
                        'list' => ['name','brand','model'],   //edit
                        'list_fake' => [],
                        'create' => [], //edit
                        'create_back' => [],
                        'create_default' => [],
                        'create_fake' => [],
                        'create_exclusion' => [],
                        'create_hidden' => [],
                        'update' => [], //edit
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
        $model = Tool_type::find($id);
        if($model['users_id'] !== null)
            $users_id = $model['users_id'];
        else
            $users_id = null;

        $this->check_permissions('delete', $users_id);

        return parent::destroy_($id, $users_id);
    }
}
