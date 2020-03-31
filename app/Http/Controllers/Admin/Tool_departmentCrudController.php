<?php

namespace App\Http\Controllers\Admin;

//use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\CrudModController;
use App\Models\Tool_department;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Tool_departmentRequest as StoreRequest;
use App\Http\Requests\Tool_departmentRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class Tool_departmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class Tool_departmentCrudController extends CrudModController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        static::$controllernew = new Tool_departmentCrudController();

        $this->crud->setModel('App\Models\Tool_department');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tool_department');
        $this->crud->setEntityNameStrings('tool department', 'tool departments');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in Tool_departmentRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');

        $this->crud->removeColumns(['tool_floors_id']);
        $this->crud->addColumn([
            'name' => 'tool_floors_id',
            'label' => 'Floor',
            'type' => 'select',
            'entity' => 'tool_floor',
            'attribute' => 'name',
            'model' => "App\Models\Tool_floor",
        ]);        
        $this->crud->orderColumns(['name','tool_floors_id']);
       
        $this->crud->removeFields(['tool_floors_id'],'both');
        $this->crud->addField([
            'name' => 'tool_floors_id',
            'label' => 'Floor',
            'type' => 'select',
            'entity' => 'tool_floor',
            'attribute' => 'name',
            'model' => "App\Models\Tool_floor",
        ], 'update/create/both');    
        
        $this->crud->orderFields(['name','tool_floors_id'],'both');

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
                    'name' => 'tool department',
                    'name_route' => 'tool_department',
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
                    'show' => ['name','tool_floors_id']
                ],
                'show_relations' => [
                    'tool_floor' => [            //edit
                        'columns' => ['name'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->tool_floor()    //edit
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
        $redirect_location = parent::storeCrud($request);
        // your additional operations after save here
        // use $this->data['entry'] or $this->crud->entry
        return $redirect_location;
    }

    public function store2(StoreRequest $request)
    {
        $this->check_permissions('sub_create');
        
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
        
        return parent::update2_($request);
    }

    public function destroy($id)
    {
        $model = Tool_department::find($id);
        if($model['users_id'] !== null)
            $users_id = $model['users_id'];
        else
            $users_id = null;

        $this->check_permissions('delete', $users_id);

        return parent::destroy_($id, $users_id);
    }

    public function getTool_departments()
    {
        $id = \Request::instance()->input()['id'];
        $repartos = Tool_department::where('tool_floors_id','=',$id)->get(['id','name'])->toArray();
        if (is_array($repartos)) {
            $repartos = $repartos;
        } else {
            $repartos = ['NULL' => 'NULL'];
        }
        return response()->json(json_encode($repartos));
    }
}
