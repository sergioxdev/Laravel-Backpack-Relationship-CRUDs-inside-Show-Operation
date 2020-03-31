<?php

namespace App\Http\Controllers\Admin;

//use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Controllers\Admin\CrudModController;
use App\Models\Tool_manual;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\Tool_manualRequest as StoreRequest;
use App\Http\Requests\Tool_manualRequest as UpdateRequest;
use Backpack\CRUD\CrudPanel;

/**
 * Class Tool_manualCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class Tool_manualCrudController extends CrudModController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | CrudPanel Basic Information
        |--------------------------------------------------------------------------
        */
        static::$controllernew = new Tool_manualCrudController();

        $this->crud->setModel('App\Models\Tool_manual');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/tool_manual');
        $this->crud->setEntityNameStrings('tool manual', 'tool manuals');

        /*
        |--------------------------------------------------------------------------
        | CrudPanel Configuration
        |--------------------------------------------------------------------------
        */

        // TODO: remove setFromDb() and manually define Fields and Columns
        $this->crud->setFromDb();

        // add asterisk for fields that are required in Tool_manualRequest
        $this->crud->setRequiredFields(StoreRequest::class, 'create');
        $this->crud->setRequiredFields(UpdateRequest::class, 'edit');
        
        $this->crud->removeColumns(['tools_id','file']);
        $this->crud->addColumn([
            'name' => 'tools_id',
            'label' => 'Tool',
            'type' => 'select',
            'entity' => 'tool',
            'attribute' => 'name',
            'model' => "App\Models\Tool",
        ]);        
        $this->crud->orderColumns(['name','tools_id','file']);

        $this->crud->removeFields(['tools_id','file'],'both');
        $this->crud->addField([
            'name' => 'tools_id',
            'label' => 'Tool',
            'type' => 'select',
            'entity' => 'tool',
            'attribute' => 'name',
            'model' => "App\Models\Tool",
        ], 'update/create/both');
        $this->crud->addField([
            'name' => 'file',
            'label' => 'File',
            'type' => 'upload',
            'disk' => 'disk',
            'prefix' => 'dir_manual',
            'filename' => 'manual.pdf',
            'private' => true,
            'upload' => true,            
            'link' => true,            
            'file' => true,
            'hint' => 'The file to be uploaded must be of the following format: PDF',
        ], 'update/create/both');    
        $this->crud->orderFields(['name','tools_id','file'],'both');

        $this->crud->allowAccess(['sub_create', 'sub_update', 'sub_delete', 'list', 'search', 'show', 'download', 'sub_download']);
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
                    'name' => 'tool manual',
                    'name_route' => 'tool_manual',
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
                    'show' => ['name','tools_id','file']
                ],
                'show_relations' => [
                    'tool' => [            //edit
                        'columns' => ['FullNome'],  //edit
                    // forward(hasOne - hasMany - belongsTo - belongsToMany)
                    // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)
                        'type_relation' => 'hasOneB',    //edit
                        'link' => true,                 //edit 
                        'entry' => $entry_->tool()    //edit
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
        // your additional operations before save here

        $check_extension = false;
        if(isset($request->file))
        {
            $file_name = $request->file->getClientOriginalName(); 
            $file_extension = '.'.$request->file->getClientOriginalExtension();
            $file_mime = $request->file->getClientMimeType();
            $length = strlen($file_extension);
            
            $check_extension = substr($file_name, - $length) === $file_extension;   
            
            if($file_extension !== ".pdf" || $file_mime !== "application/pdf" || !$check_extension)
            {
                return response()->json(['message' => 'The given data was invalid.', 'errors' => [
                    'file' => trans("The file format is invalid.")
                        ]
                ], 422);
            }
        }        
        else
        {
            if($request->request->get('file')==NULL)
            {
                return response()->json(['message' => 'The given data was invalid.', 'errors' => [
                    'file' => trans("The file field is required.")
                        ]
                ], 422);
            }

        }
 
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

        $check_extension = false;
        if(isset($request->file))
        {
            $file_name = $request->file->getClientOriginalName(); 
            $file_extension = '.'.$request->file->getClientOriginalExtension();
            $file_mime = $request->file->getClientMimeType();
            $length = strlen($file_extension);
            
            $check_extension = substr($file_name, - $length) === $file_extension;   
            
            if($file_extension !== ".pdf" || $file_mime !== "application/pdf" || !$check_extension)
            {
                return response()->json(['message' => 'The given data was invalid.', 'errors' => [
                    'file' => trans("The file format is invalid.")
                        ]
                ], 422);
            }
        }        
        else
        {
            if($request->request->get('file')==NULL)
            {
                return response()->json(['message' => 'The given data was invalid.', 'errors' => [
                    'file' => trans("The file field is required.")
                        ]
                ], 422);
            }

        }

        return parent::update2_($request);
    }

    public function destroy($id)
    {
        $model = Tool_manual::find($id);
        if($model['users_id'] !== null)
            $users_id = $model['users_id'];
        else
            $users_id = null;

        $this->check_permissions('delete', $users_id);

        $disk = "disk";
        if(isset($model['file']) && $model['file'] != NULL && \Storage::disk($disk)->exists($model['file']))
            \Storage::disk($disk)->delete($model['file']);

        return parent::destroy_($id, $users_id);
    }
}
