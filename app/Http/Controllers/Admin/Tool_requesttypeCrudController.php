<?php

namespace App\Http\Controllers\Admin;

//use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\CrudModController;
use App\Models\Tool_requesttype;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Tool_requesttypeRequest as StoreRequest;
use App\Http\Requests\Tool_requesttypeRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class Tool_requesttypeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class Tool_requesttypeCrudController extends CrudModController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        static::$controllernew = new Tool_requesttypeCrudController();

        $this->crud->setModel('App\Models\Tool_requesttype');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tool_requesttype');
        $this->crud->setEntityNameStrings('tool request type', 'tool request types');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in Tool_requesttypeRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');

        $this->crud->removeColumns(['color','tool_requestprioritys_id']);
        $this->crud->addColumn([
            'name' => 'color',
            'label' => 'Color',
            'type' => 'color',
        ]);
        $this->crud->addColumn([
            'name' => 'tool_requestprioritys_id',
            'label' => 'Request priority',
            'type' => 'select',
            'entity' => 'tool_requestpriority',
            'attribute' => 'name',
            'model' => "App\Models\Tool_requestpriority",
        ]);
        $this->crud->orderColumns(['name','color','tool_requestprioritys_id']);

        $this->crud->removeFields(['color','tool_requestprioritys_id'],'both');
        $this->crud->addField([
            'name' => 'color',
            'label' => 'Color',
            'type' => 'color_picker',
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'tool_requestprioritys_id',
            'label' => 'Request priority',
            'type' => 'select',
            'entity' => 'tool_requestpriority',
            'attribute' => 'name',
            'model' => "App\Models\Tool_requestpriority",
        ], 'update/create/both');        
        $this->crud->orderFields(['name','color','tool_requestprioritys_id'],'both');

        $this->crud->allowAccess(['create', 'update', 'delete', 'list', 'search']);
        $this->crud->denyAccess(['show', 'sub_create', 'sub_update', 'sub_delete']);
        $this->crud->setShowView('admin/show_');
        $this->crud->enableAjaxTable();
        $this->check_permissions('list');
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
        $model = Tool_requesttype::find($id);
        if($model['users_id'] !== null)
            $users_id = $model['users_id'];
        else
            $users_id = null;

        $this->check_permissions('delete', $users_id);

        return parent::destroy_($id, $users_id);
    }
}
