<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\Exception\AccessDeniedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\BackpackUser;
use App\User;
use Illuminate\Support\Facades\Route;
use Backpack\CRUD\CrudPanel;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Hash;

class CrudModController extends CrudController
{
	public static $controllernew;
	//Administrator
	public static $admin_array = ['Administrator'];
    //User
    public static $user_array = ['User'];
	// your additional Role
	
	// Must be exact 32 chars (256 bit)
    protected static $open_method_ = 'AES-256-CBC';
    protected static $open_key_ = "!$%_YourPassword321XXXxxx0!$%&32";
	

	public function __construct()
    {
    	static::$controllernew = "";

    	return parent::__construct();

    }

    /*-----------------------------------------------------------------------*/
    /*
        ->  getCrud
        ->  getSubFieldsCreateUpdate
        ->  getSubFieldsCreateUpdate_
        ->  checkFieldsCreateUpdate
        ->  show_
        ->  store2_
        ->  update2_
        ->  destroy_
        ->  check_permissions

        ->  search
        ->  getEntriesAsJsonForDatatables_
    */
    /*-----------------------------------------------------------------------*/

    public function index()
    {
        $user = backpack_user();
        $permissions_name = User::getPermissionByUser($user->id, Route::currentRouteName());

        $this->check_permissions('list', $user->id);

        foreach ($this->crud->access as $key => $permission_name)
        {
            if(!in_array($permission_name, $permissions_name))
            {
                unset($this->crud->access[$key]);
            }

        }
        // your additional operations

        $redirect_location = parent::index();

        return $redirect_location;
    }

    public function getCrud($sub_crud_, $crud_name_route_, $crud_relation_id_)
    {
        $this->setup();
        //----------------------------------------------------------------------------
        $crud_name_route_ = $crud_name_route_;
        $crud_relation_id_ = $crud_relation_id_;
        $name_route_ = $sub_crud_["name_route"];
        $relation_type_ = $sub_crud_["relation_type"];
        $relation_name_ = $sub_crud_["relation_name"];
        $fields_ = $sub_crud_["fields"];
        $fields_pivot_ = $sub_crud_["fields_pivot"];
        $fields_mod_ = $sub_crud_["fields_mod"];
        $show_relations_ = $sub_crud_["show_relations"];
        $enable_reorder_ = $sub_crud_["enable_reorder"];

        if(isset($crud_name_route_) && isset($crud_relation_id_) && isset($name_route_) && isset($relation_type_))
        {
            if($relation_type_ == "hasOne" || $relation_type_ == "hasOneB")
            {
                $this->crud->addClause('where', $crud_name_route_.'s_id', $crud_relation_id_); 

            }
            elseif($relation_type_ == "hasMany" || $relation_type_ == "hasManyB")
            {
				$this->crud->addClause('where', $crud_name_route_.'s_id', $crud_relation_id_); 
            }
            elseif($relation_type_ == "belongsTo" || $relation_type_ == "belongsToB")
            {
                
                
            }
            elseif($relation_type_ == "belongsToMany" || $relation_type_ == "belongsToManyB")
            {
                if($relation_type_ == "belongsToMany")
                {
                    $this->crud->addClause('whereHas', $relation_name_, function($query)
                        use($name_route_, $crud_relation_id_)
                    {
                        $query->where($name_route_.'s_id', '=',$crud_relation_id_);
                    });
                }
                elseif($relation_type_ == "belongsToManyB")
                {
                    $this->crud->addClause('whereHas', $crud_name_route_.'s', function($query)
                        use($name_route_, $crud_relation_id_)
                    {
                        $query->where($name_route_.'s_id', '=',$crud_relation_id_);
                    });

                }
            }
        }
        if(isset($relation_name_))
        {
            //$_POST["relation_name"]
        }

        if(isset($fields_["list"]) && !empty($fields_['list']))
        {
            foreach ($this->crud->model->getFillable() as $key => $column)
            {
                if(!in_array($column, $fields_["list"]))
                {
                    $this->crud->removeColumn($column);
                }
            }
        }
        
        if(isset($fields_["list"]) && !empty($fields_['list']) && isset($fields_["list_fake"]) && !empty($fields_['list_fake']))
        {
            foreach ($fields_["list_fake"] as $key => $column)
            {
                $this->crud->removeColumn($column);
                $this->crud->addColumn([
                    'label' => $column,
                    'name' => $column
                ]);
            }
        }

        if(isset($fields_pivot_))
        {
            foreach ($fields_pivot_ as $key => $column)
            { 
                $this->crud->addColumn($column);
            }
        }

        if(isset($fields_mod_))
        {
            foreach ($fields_mod_ as $key => $column)
            {
                $this->crud->removeField($key, 'update/create/both');
                $this->crud->addField($column, 'update/create/both');;
            }
        }

        if(isset($show_relations_))
        {
            foreach ($show_relations_ as $key => $column)
            { 
                $this->crud->modifyColumn($key, $column);
            }
        }

        if(isset($_POST["link"]))
        {
            //$_POST["link"]
        }

        $this->checkFieldsCreateUpdate($fields_);

        if(isset($enable_reorder_))
        {
            if(isset($enable_reorder_['attribute_name']) && isset($enable_reorder_['depth']))
            $this->crud->enableReorder($enable_reorder_['attribute_name'], $enable_reorder_['depth']);
        }

        return $this->crud;
    }

    public function getSubFieldsCreateUpdate($sub_crud_, $crud_relation_id_)
    {
        $this->setup();
        $options_array = array();

        $crud_relation_id_ = $crud_relation_id_;
       
        $relation_type_ = $sub_crud_["relation_type"];
        $fields_ = $sub_crud_["fields"];
        $subcrud_relation_id_ = $sub_crud_["relation_id"];
        $enable_reorder_ = $sub_crud_["enable_reorder"];

        $this->checkFieldsCreateUpdate($fields_);

        if(isset($enable_reorder_))
        {
            if(isset($enable_reorder_['attribute_name']) && isset($enable_reorder_['depth']))
            $this->crud->enableReorder($enable_reorder_['attribute_name'], $enable_reorder_['depth']);
        }

        return $this->crud;
    }

    public static function getSubFieldsCreateUpdate_($sub_crud_, $crud_relation_id_, $crud_=null)
    {
        if($crud_!==null)
            $crud = $crud_;
        else
            $crud = static::$controllernew;

        $crudd = $crud->getSubFieldsCreateUpdate($sub_crud_, $crud_relation_id_);
        return $crudd;
    }

    public function checkFieldsCreateUpdate($fields_)
    {
        if((isset($fields_["create"]) || isset($fields_["update"])) && (!empty($fields_['create']) || !empty($fields_['update'])) && isset($fields_["create_back"]) && isset($fields_["create_default"]) && isset($fields_["create_fake"]) && isset($fields_["create_exclusion"]) && isset($fields_["create_hidden"]) && isset($fields_["update_back"]) && isset($fields_["update_default"]) && isset($fields_["update_fake"]) && isset($fields_["update_exclusion"]) && isset($fields_["update_hidden"]))
        {               
            $fields_crud = $this->crud->model->getFillable();

            foreach ($fields_crud as $key => $value)
            {                
                $check_create_exists = false;
                $check_update_exists = false;
                $check_create_back_exists = false;
                $check_update_back_exists = false;

                if(!in_array($value, $fields_["create"]))
                    $check_create_exists = true;
                if(!in_array($value, $fields_["update"]))
                    $check_update_exists = true;

                

                if($check_create_exists || $check_update_exists)
                {                    
                    $check_create_exists && !$check_update_exists ? $remove_str = 'create' : ''; 
                    !$check_create_exists && $check_update_exists ? $remove_str = 'update' : ''; 
                    $check_create_exists && $check_update_exists ? $remove_str = 'update/create/both' : ''; 
                    if (isset($remove_str))
                    {
                        $this->crud->removeField($value, $remove_str);

                        if(strpos($remove_str, "create")!==false && isset($this->crud->requiredFields['create']))
                            foreach($this->crud->requiredFields['create'] as $key_ => $value_) 
                            {
                                if (stripos($value_, $value)!==false)
                                    unset($this->crud->requiredFields['create'][$key_]);

                                if (stripos($value_, str_replace("_id","",$value))!==false)
                                    unset($this->crud->requiredFields['create'][$key_]);
                            }
                        
                        if(strpos($remove_str, "update")!==false && isset($this->crud->requiredFields['edit']))
                            foreach($this->crud->requiredFields['edit'] as $key_ => $value_) 
                            {
                                if (stripos($value_, $value)!==false)
                                    unset($this->crud->requiredFields['edit'][$key_]);
                                if (stripos($value_, str_replace("_id","",$value))!==false)
                                    unset($this->crud->requiredFields['edit'][$key_]);
                            }
                    }
                }

                if(isset($fields_["create_back"]) && isset($fields_["create_back"][$value]) && array_key_exists($value, $fields_["create_back"]))
                    $check_create_back_exists = true;
                if(isset($fields_["update_back"]) && isset($fields_["update_back"][$value]) && array_key_exists($value, $fields_["update_back"]))
                    $check_update_back_exists = true;

                if($check_create_back_exists || $check_update_back_exists)
                {                         
                    $check_create_back_exists && !$check_update_back_exists ? $remove_str = 'create' : ''; 
                    !$check_create_back_exists && $check_update_back_exists ? $remove_str = 'update' : ''; 
                    $check_create_back_exists && $check_update_back_exists ? $remove_str = 'update/create/both' : ''; 

                    if (isset($remove_str))
                    {
                        $this->crud->removeField($value);
                        $this->crud->addField([
                            'label' => ucfirst(str_replace('_', '', str_replace("s_id","",$value))),
                            'type' => $fields_["create_back"][$value]['type'],
                            'name' => $value,
                            'options' => $fields_["create_back"][$value]['options'],
                            'allows_null' => false,
                        ], $remove_str);
                    }
                }
            }
            
            foreach ($fields_crud as $key => $value)
            {                
                $check_create_default_exists = false;
                $check_update_default_exists = false;

                if(in_array($value, $fields_["create_default"]))
                    $check_create_default_exists = true;
                if(in_array($value, $fields_["update_default"]))
                    $check_update_default_exists = true;

                if($check_create_default_exists || $check_update_default_exists)
                {
                    $check_create_default_exists && !$check_update_default_exists ? $remove_str = 'create' : ''; 
                    !$check_create_default_exists && $check_update_default_exists ? $remove_str = 'update' : ''; 
                    $check_create_default_exists && $check_update_default_exists ? $remove_str = 'update/create/both' : '';

                    if (isset($remove_str))
                    {
                        $this->crud->removeField($value, $remove_str);
                        $this->crud->addField([
                            'type' => 'hidden',
                            'name' => $value,
                            'value' => 0
                        ], $remove_str);
                    }
                }

            }

            if(!empty($fields_['create_fake']) || !empty($fields_['update_fake']))
            {
                $check_create_fake_exists = $fields_['create_fake'];
                $check_update_fake_exists = $fields_['update_fake'];

                $check_create_fake_exists && !$check_update_fake_exists ? $remove_str = 'create' : ''; 
                !$check_create_fake_exists && $check_update_fake_exists ? $remove_str = 'update' : ''; 
                $check_create_fake_exists && $check_update_fake_exists ? $remove_str = 'update/create/both' : '';
                if (isset($remove_str))
                {
                    if(strpos($remove_str, "create")!==false)
                        $this->crud->addField([
                            'type' => 'hidden',
                            'name' =>$fields_['create_fake'][0],
                            'value' => 'true'
                        ]);
                         
                    if(strpos($remove_str, "update")!==false)
                        $this->crud->addField([
                            'type' => 'hidden',
                            'name' => $fields_['update_fake'][0],
                            'value' => 'true'
                        ], $remove_str);
                }
            }
            $fields_crud_c = $this->crud->getCurrentFields();
            $fields_crud__c = array();
            foreach ($fields_crud_c as $key => $value)
            {
                $fields_crud__c[] = $key;
            }

            $fields_crud = array_merge($fields_crud, $fields_crud__c);
            
            foreach ($fields_crud as $key => $value)
            {                
                $check_create_exclusion_exists = false;
                $check_update_exclusion_exists = false;

                if(in_array($value, $fields_["create_exclusion"]))
                    $check_create_exclusion_exists = true;
                if(in_array($value, $fields_["update_exclusion"]))
                    $check_update_exclusion_exists = true;

                if($check_create_exclusion_exists || $check_update_exclusion_exists)
                {
                    $check_create_exclusion_exists && !$check_update_exclusion_exists ? $remove_str = 'create' : ''; 
                    !$check_create_exclusion_exists && $check_update_exclusion_exists ? $remove_str = 'update' : ''; 
                    $check_create_exclusion_exists && $check_update_exclusion_exists ? $remove_str = 'update/create/both' : '';

                    if (isset($remove_str))
                    {
                        $this->crud->removeField($value, $remove_str);
                    }
                }
            }

            foreach ($fields_crud as $key => $value)
            {                
                $check_create_hidden_exists = false;
                $check_update_hidden_exists = false;

                if(in_array($value, $fields_["create_hidden"]))
                    $check_create_hidden_exists = true;
                if(in_array($value, $fields_["update_hidden"]))
                    $check_update_hidden_exists = true;

                if($check_create_hidden_exists || $check_update_hidden_exists)
                {
                    $check_create_hidden_exists && !$check_update_hidden_exists ? $remove_str = 'create' : ''; 
                    !$check_create_hidden_exists && $check_update_hidden_exists ? $remove_str = 'update' : ''; 
                    $check_create_hidden_exists && $check_update_hidden_exists ? $remove_str = 'update/create/both' : '';

                    if (isset($remove_str))
                    {
                        $this->crud->removeField($value, $remove_str);
                        $this->crud->addField([
                            'type' => 'hidden',
                            'name' => $value
                        ], $remove_str);
                    }
                }
            }
        }

        //------------------------------------------------
        $delete_ = false; 
        if(isset($fields_["create"]) && empty($fields_['create'])
            && isset($fields_["create_back"]) && empty($fields_['create_back'])
            && isset($fields_["create_default"]) && empty($fields_['create_default'])
            && isset($fields_["create_fake"]) && empty($fields_['create_fake'])
            && isset($fields_["create_exclusion"]) && empty($fields_['create_exclusion'])
            && isset($fields_["create_hidden"]) && empty($fields_['create_hidden']))
        {
            $key = array_search ('sub_create', $this->crud->access);
            unset($this->crud->access[$key]);
            $delete_ = true;
        }
        if(isset($fields_["update"]) && empty($fields_['update'])
            && isset($fields_["update_back"]) && empty($fields_['update_back'])
            && isset($fields_["update_default"]) && empty($fields_['update_default'])
            && isset($fields_["update_fake"]) && empty($fields_['update_fake'])
            && isset($fields_["update_exclusion"]) && empty($fields_['update_exclusion'])
            && isset($fields_["update_hidden"]) && empty($fields_['update_hidden']))
        {
            $key = array_search ('sub_update', $this->crud->access);
            unset($this->crud->access[$key]);
            $delete_ = true;   
        }

        if($delete_)
        {
            $key = array_search ('sub_delete', $this->crud->access);
            unset($this->crud->access[$key]); 
        }
        //------------------------------------------------
    }

    public function show_()
    {        
        if(isset($this->crud->sub_entry_) && isset($this->crud->sub_entry_['crud']) && isset($this->crud->sub_entry_['crud']['fields']) && isset($this->crud->sub_entry_['crud']['fields']['type']))
        {
            foreach ($this->crud->sub_entry_['crud']['fields']['type'] as $key => $column)
            {
                $column['label'] = ucfirst(str_replace('_', ' ', $key));
                $column['name'] = $key;
                $this->crud->addField($column);
            }
        }

        // your additional operations

        return view($this->crud->getShowView(), $this->data);
    }

    public function store2_($request)
    {
        $this->crud->hasAccessOrFail('sub_create');
        
        // fallback to global request instance
        if (is_null($request)) {
            $request = \Request::instance();
        }

        // replace empty values with NULL, so that it will work with MySQL strict mode on
        foreach ($request->input() as $key => $value) {
            if (empty($value) && $value !== '0') {
                $request->request->set($key, null);
            }
        }

        // insert item in the db
        $item = $this->crud->create($request->except(['save_action', '_token', '_method']));
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        return \Alert::success(trans('backpack::crud.insert_success'))->flash();
    }

    public function update2_($request)
    {
        $this->crud->hasAccessOrFail('sub_update');
        // your additional operations before save here

        // fallback to global request instance
        if (is_null($request)) {
            $request = \Request::instance();
        }

        // replace empty values with NULL, so that it will work with MySQL strict mode on
        foreach ($request->input() as $key => $value) {
            if (empty($value) && $value !== '0') {
                $request->request->set($key, null);
            }
        }

        // update the row in the db
        $item = $this->crud->update($request->get($this->crud->model->getKeyName()),
                            $request->except('save_action', '_token', '_method'));
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        return \Alert::success(trans('backpack::crud.update_success'))->flash();
    }

    public function destroy_($id, $users_id)
    {
        if($users_id !== null)
            $this->check_permissions('sub_delete', $users_id);

        if($this->crud->hasAccess('sub_delete'))
            return $this->crud->delete($id);
  
        if($users_id !== null)
            $this->check_permissions('delete', $users_id);

        return parent::destroy($id);
    }

    public function reorder2($parent_table, $id, $attribute_name, $depth)
    {
        $this->check_permissions('sub_reorder');
        $this->crud->hasAccessOrFail('sub_reorder');

        $this->crud->addClause('where', $parent_table.'_id', $id);

        $this->crud->enableReorder($attribute_name, $depth);

        if (! $this->crud->isReorderEnabled()) {
            abort(403, 'Reorder is disabled.');
        }

        // get all results for that entity
        $this->data['entries'] = $this->crud->getEntries();
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? trans('backpack::crud.reorder').' '.$this->crud->entity_name;

        $this->crud->setReorderView('admin/reorder_');
        // load the view from /resources/views/vendor/backpack/crud/ if it exists, otherwise load the one in the package
        return view($this->crud->getReorderView(), $this->data);
    }

    public function saveReorder2()
    {
        $this->check_permissions('sub_reorder');
        $this->crud->hasAccessOrFail('sub_reorder');
        $this->crud->setOperation('reorder');

        $all_entries = \Request::input('tree');

        if (count($all_entries)) {
            $count = $this->crud->updateTreeOrder($all_entries);
        } else {
            return false;
        }

        return 'success for '.$count.' items';
    }

    public function check_permissions($permission, $users_id = null)
    {
        $permission_ = str_replace("crud.", "", Route::currentRouteName());
        $pos = strpos($permission_, ".");
        if($pos>0)
        $permission__ = substr($permission_, $pos+1, strlen($permission_));
        $table_name = $this->crud->model->getTable();
        if($permission__ == "store")
            $permission = 'create';
        else if($permission__ == "update")
            $permission = 'update';
        if($pos>0)
        $permission_ = substr($permission_, 0, $pos).".".$permission;

        $user = backpack_user();
        $users_array = array_merge(self::$admin_array, self::$user_array);

        $check_suser = User::getCheckUserByRole($user->id, $users_array);
        $check_suser1 = User::getCheckUserByPermission($user->id, $permission_);
        $check_column_users_id = User::getCheckColumnUsersid($table_name);

        if($permission=='list' || $permission=='sub_list')
        {
            if(!$check_suser && $check_column_users_id)
            {
                $this->crud->addClause('where', 'users_id', $user->id);
            }

            if(!$check_suser && !$check_suser1)
            {
                throw new AccessDeniedException(trans('backpack::crud.unauthorized_access', ['access' => $permission]));
            }
            $this->crud->hasAccessOrFail($permission);
        }
        elseif($permission=='show' || $permission=='sub_show')
        {
            if(!$check_suser && $check_column_users_id)
            {
                $this->crud->addClause('where', 'users_id', $user->id);
            }

            if(!$check_suser && (!$check_suser1 || ($check_column_users_id && $users_id !== $user->id)))
            {
                throw new AccessDeniedException(trans('backpack::crud.unauthorized_access', ['access' => $permission]));
            }

            $this->crud->hasAccessOrFail($permission);
        }
        elseif($permission=='create' || $permission=='sub_create' ||$permission=='update' || $permission=='sub_update' || $permission=='delete' || $permission=='sub_delete' || $permission=='destroy' || $permission=='download' || $permission=='sub_download')
        {
            if(!$check_suser && (!$check_suser1 || ($check_column_users_id && $users_id !== $user->id)))           
            {
                throw new AccessDeniedException(trans('backpack::crud.unauthorized_access', ['access' => $permission]));
            }
            
            return $this->crud->hasAccessOrFail($permission);
        }        
    }

    public function getPermissionsUser()
    {
        $user = backpack_user();
        $permissions_name = User::getPermissionByUser($user->id, Route::currentRouteName());

        foreach ($this->crud->access as $key => $permission_name)
        {
            if(!in_array($permission_name, $permissions_name))
            {
                unset($this->crud->access[$key]);
            }

        }
		
        $this->crud->search_mod = false;

    }

    public function parsDataJson()
    {
        //-------------------------------------------------
        if(isset($_POST["data_json_encrypted"]))
        {
            $data_json_encrypted_ = $_POST["data_json_encrypted"];
            $data_json_encrypted_ = json_decode(open_decrypt($data_json_encrypted_), true);

            if(isset($data_json_encrypted_["search_mod"]) && $data_json_encrypted_['search_mod'])
            {
                $this->crud->search_mod = true;
            }
        }
        //-------------------------------------------------


        if(isset($_POST["data_json_encrypted"]))
        {
            $data_json_encrypted_ = $_POST["data_json_encrypted"];
            $data_json_encrypted_ = json_decode(open_decrypt($data_json_encrypted_), true);

            if(isset($data_json_encrypted_["search_mod"]) && $data_json_encrypted_['search_mod'])
            {
                //---------------------------------------------------------------
                if(isset($data_json_encrypted_["crud_name_route"]) && isset($data_json_encrypted_["crud_relation_id"]) && isset($data_json_encrypted_["sub_name_route"]) && isset($data_json_encrypted_["sub_relation_type"]))
                {
                    $crud_name_route_ = $data_json_encrypted_["crud_name_route"];
                    $crud_relation_id_ = $data_json_encrypted_["crud_relation_id"];
                    $sub_name_route_ = $data_json_encrypted_["sub_name_route"];
                    $sub_relation_type_ = $data_json_encrypted_["sub_relation_type"];
                    $sub_relation_name_ = $data_json_encrypted_["sub_relation_name"];
                    $sub_relation_id_ = $data_json_encrypted_["sub_relation_id"];
                    $search_mod = $data_json_encrypted_['search_mod'];

                    if($sub_relation_type_ == "hasOne" || $sub_relation_type_ == "hasOneB")
                    {
                         $this->crud->addClause('where', $crud_name_route_.'s_id', $crud_relation_id_);
                    }
                    elseif($sub_relation_type_ == "hasMany" || $sub_relation_type_ == "hasManyB")
                    {
                        $this->crud->addClause('where', $crud_name_route_.'s_id', $crud_relation_id_); 
                    }
                    elseif($sub_relation_type_ == "belongsTo" || $sub_relation_type_ == "belongsToB")
                    {
                        //$this->crud->addClause('where', $crud_name_route_.'s_id', $crud_relation_id_); 
                    }
                    elseif($sub_relation_type_ == "belongsToMany" || $sub_relation_type_ == "belongsToManyB")
                    {       
                        if($sub_relation_type_ == "belongsToMany")
                        {
                            $this->crud->addClause('whereHas', $sub_relation_name_, function($query)
                                use($crud_name_route_, $crud_relation_id_)
                            {
                                $query->where($crud_name_route_.'s_id', '=',$crud_relation_id_);
                            });
                        }
                        elseif($sub_relation_type_ == "belongsToManyB")
                        {
                            $this->crud->addClause('whereHas', $sub_relation_name_, function($query)
                                use($crud_name_route_, $crud_relation_id_)
                            {
                                $query->where($crud_name_route_.'s_id', '=',$crud_relation_id_);
                            });
                        } 
                    }
                    
                    $fields_ = [];
                    $fields_['list'] = [];
                    $fields_['list_fake'] = [];
                    $fields_['create'] = [];
                    $fields_['create_back'] = [];
                    $fields_["create_default"] = [];
                    $fields_["create_fake"] = [];
                    $fields_["create_hidden"] = [];
                    $fields_["create_exclusion"] = [];
                    $fields_['update'] = [];
                    $fields_['update_back'] = [];                   
                    $fields_["update_default"] = [];
                    $fields_["update_fake"] = [];                   
                    $fields_["update_exclusion"] = [];
                    $fields_["update_hidden"] = [];

                    if(isset($data_json_encrypted_["fields"]))
                    {
                        $fields_ = $data_json_encrypted_["fields"];
                        $fields_['list'] = $data_json_encrypted_["fields"]['list'];
                        $fields_['list_fake'] = $data_json_encrypted_["fields"]['list_fake'];                       
                        $fields_['create'] = $data_json_encrypted_["fields"]['create'];
                        $fields_['create_back'] = $data_json_encrypted_["fields"]['create_back'];
                        $fields_['create_default'] = $data_json_encrypted_["fields"]['create_default'];
                        $fields_['create_fake'] = $data_json_encrypted_["fields"]['create_fake'];
                        $fields_['create_exclusion'] = $data_json_encrypted_["fields"]['create_exclusion'];
                        $fields_['create_hidden'] = $data_json_encrypted_["fields"]['create_hidden'];
                        $fields_['update'] = $data_json_encrypted_["fields"]['update'];
                        $fields_['update_back'] = $data_json_encrypted_["fields"]['update_back'];                        
                        $fields_['update_default'] = $data_json_encrypted_["fields"]['update_default'];
                        $fields_['update_fake'] = $data_json_encrypted_["fields"]['update_fake'];
                        $fields_['update_exclusion'] = $data_json_encrypted_["fields"]['update_exclusion'];  
                        $fields_['update_hidden'] = $data_json_encrypted_["fields"]['update_hidden'];  

                        if(isset($fields_["list"]) && !empty($fields_['list']))
                        {
                            foreach ($this->crud->model->getFillable() as $key => $column)
                            {
                                if(!in_array($column, $fields_["list"]))
                                {
                                    $this->crud->removeColumn($column);
                                }
                            }
                        }

                        if(isset($fields_["list_fake"]) && !empty($fields_['list_fake']))
                        {
                            foreach ($fields_["list_fake"] as $key => $column)
                            {
                                $this->crud->removeColumn($column);
                                $this->crud->addColumn([
                                    'label' => $column,
                                    'name' => $column
                                ]);
                            }
                        }

                        $this->checkFieldsCreateUpdate($fields_);
                    }

                    if(isset($data_json_encrypted_["fields_pivot"]))
                    {   
                        $fields_pivot = [];
                        foreach ($data_json_encrypted_["fields_pivot"] as $key => $column) {
                            foreach ($column as $key_ => $value_) {
                                $fields_pivot[$key][$key_] = $value_;
                            }
                            $this->crud->addColumn($column);
                        }
                    }

                    if(isset($data_json_encrypted_["fields_mod"]))
                    {   
                        $fields_mod = [];
                        foreach ($data_json_encrypted_["fields_mod"] as $key => $column) {
                            foreach ($column as $key_ => $value_) {
                                $fields_mod[$key][$key_] = $value_;
                            }
							
                            $this->crud->removeField($key, 'update/create/both');
                            $this->crud->addField($column);
                        }
                    }else{
                        $fields_mod = [];
                    }

                    if(isset($data_json_encrypted_['show_relations']))
                    {
                        $show_relations_ = [];
                        foreach ($data_json_encrypted_['show_relations'] as $key => $column)
                        {
                            $show_relations_[$key] = $column;
                            $this->crud->modifyColumn($key, $column);
                        }
                    }
                    

                    if(isset($_POST["link"]))
                    {
                        //$_POST["link"]
                    }

                    if(isset($data_json_encrypted_["enable_reorder"]))
                    {
                        $enable_reorder_ = $data_json_encrypted_["enable_reorder"];

                        if(isset($enable_reorder_['attribute_name']) && isset($enable_reorder_['depth']))
                        {
                            $this->crud->enableReorder($enable_reorder_['attribute_name'], $enable_reorder_['depth']);

                            $enable_reorder_ = $data_json_encrypted_["enable_reorder"];
                        }
                        else
                        {
                            $enable_reorder_ = [];
                        }


                    }
                    else
                    {
                        $enable_reorder_ = [];
                    }
                    //---------------------------------------------------------------
                    $controller_new = [
                        'sub_controller_new' => [
                            '0' => [
                                'name' => $sub_name_route_,
                                'controller' =>  static::$controllernew,  //edit
                            ]
                        ],
                    ];
                    $this->cruds_name_array = $controller_new['sub_controller_new'][0]['name'];
                    $this->sub_entry_ = [
                        'crud' => [
                            'name_route' => $crud_name_route_,
                            'relation_id' => $crud_relation_id_,
                            'search_mod' => $search_mod
                        ],
                        'sub_crud_relations' => [
                            $controller_new['sub_controller_new'][0]['name'] => [
                                'name_route' => $controller_new['sub_controller_new'][0]['name'],
                                'name' => str_replace("_"," ", $controller_new['sub_controller_new'][0]['name']),
                                'controller_new' => $controller_new['sub_controller_new'][0]['controller'],
                                // forward(hasOne - hasMany - belongsTo - belongsToMany)
                                // backward(hasOneB - hasManyB - belongsToB - belongsToManyB)                       
                                'relation_type' => $sub_relation_type_,     //edit
                                'relation_name' => $sub_relation_name_,         //edit
                                'relation_id' =>  $sub_relation_id_,   //edit
                                'fields' => [
                                    'list' => $fields_['list'],
                                    'list_fake' => $fields_['list_fake'],       
                                    'create' => $fields_['create'],
                                    'create_back' => $fields_['create_back'],
                                    'create_default' => $fields_['create_default'],
                                    'create_fake' => $fields_['create_fake'],
                                    'create_exclusion' => $fields_['create_exclusion'],
                                    'create_hidden' => $fields_['create_hidden'],
                                    'update' => $fields_['update'], //edit
                                    'update_back' => $fields_['update_back'],
                                    'update_default' => $fields_['update_default'],
                                    'update_fake' => $fields_['update_fake'],
                                    'update_exclusion' => $fields_['update_exclusion'],
                                    'update_hidden' => $fields_['update_hidden'],
                                ],
                                'fields_pivot' => $fields_pivot,
                                'fields_mod' => $fields_mod,
                                'show_relations' => $show_relations_, //edit
                                'enable_reorder' => $enable_reorder_,
                            ]
                        ],
                    ];
                    //---------------------------------------------------------------

                    $this->crud->sub_entry_ = $this->sub_entry_;
                    $this->crud->cruds_name_array = $this->cruds_name_array;
                }
            }
        }
    }

	public function search()
    {
        $this->crud->hasAccessOrFail('list');
        $this->crud->setOperation('list');

        $this->getPermissionsUser();
        $this->parsDataJson();

        return parent::search();
    }


    public function downloadFile(Request $request)
    {
        $this->check_permissions('download');

        $dat_ = $request['dat_'];
        $pos_dk = strpos($dat_, "|");
        $disk = substr($dat_, 0, $pos_dk);
        $dat_ = substr($dat_, $pos_dk+1, strlen($dat_)-$pos_dk);
        $pos_ff = strpos($dat_, "|");
        $file_name_ff = substr($dat_, 0, $pos_ff);
        $dat_ = substr($dat_, $pos_ff+1, strlen($dat_)-$pos_ff);
        $pos_dy = strpos($dat_, "/");
        $directory = substr($dat_, 0, $pos_dy);//+1, $pos_dy-$pos_dk-1);
        $file_name = substr($dat_, $pos_dy+1, strlen($dat_)-$pos_dy);
        $pos_ext = strpos($file_name, ".");
        $ext = substr($file_name, $pos_ext+1, strlen($file_name)-$pos_ext);

        
        if(isset($disk) && $disk != "" && isset($directory) && $directory != "" && isset($file_name) && $file_name != "" && isset($ext) && $ext != "")
        {
            $file_name_ff = ($file_name_ff == "") ? $file_name : $file_name_ff;

            $current_time = Carbon::now()->timestamp;
            $current_time_hash = Hash::make($current_time);
            $current_time_hash_length = strlen($current_time_hash);
            $current_time_hash = substr($current_time_hash, 30, $current_time_hash_length-30);
            $current_time_hash = preg_replace('/[^A-Za-z0-9-]/', '', $current_time_hash);
            
            $dir_cen = "/".$disk."/".$directory."/".$current_time_hash."/";
            $dir_cen_ = storage_path('app/public').$dir_cen;


            $myfile = Storage::disk($disk)->get($directory."/".$file_name);

            Storage::disk('public')->put($dir_cen.$file_name_ff,$myfile);

            $ext_ct = "";
            if(strtolower($ext)=="zip")
                $ext_ct = "application/zip";
            if(strtolower($ext)=="pdf")
                $ext_ct = "application/pdf";

            $f_file = $dir_cen_.$file_name_ff;

            $response = response(Storage::disk('public')->get($dir_cen.$file_name_ff));
            $response->header('Content-type', $ext_ct);

            $response->header('Content-disposition', 'attachment; filename="'.$file_name_ff.'"');
            $response->header('Content-length', filesize($f_file));
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', '0');
            $response->header('Pragma', 'no-cache');
            
            Storage::disk('public')->delete($dir_cen.$file_name_ff);
            Storage::disk('public')->deleteDirectory($dir_cen);

            return $response;
        }
        else
        {
            return response()->json(['success' => false, 'data' => [ 'access' => 'Access not granted!']]);
        }      
    }
	
	
	
	public static function open_encrypt($data, $key = null, $iv = null, $method = null)
	{
		$key = ($key !== null) ? substr(hash('sha256', $key, true), 0, 32) : substr(hash('sha256', self::$open_key_, true), 0, 32);

	    $method = ($method !== null) ? $method : self::$open_method_;	    

        if($iv !== null)
        {
            $iv = substr(hash('sha256', $iv, true),0,16);
            $ivSize = strlen($iv);
        }
        else
        {
	       $ivSize = openssl_cipher_iv_length($method);
	       $iv = openssl_random_pseudo_bytes($ivSize);
        }	   

	    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
	    
	    // For storage/transmission, we simply concatenate the IV and cipher text
	    $encrypted = base64_encode($iv . $encrypted);

	    return $encrypted;
	}

	public static function open_decrypt($data, $key = null, $iv = null, $method = null)
	{
		$key = ($key !== null) ? substr(hash('sha256', $key, true), 0, 32) : substr(hash('sha256', self::$open_key_, true), 0, 32);
	    $method = ($method !== null) ? $method : self::$open_method_;	   

	    $data = base64_decode($data);
        if($iv !== null)
        {
            $iv = substr($iv, 0, $ivSize);
            $iv = substr(hash('sha256', $iv, true),0,16);
        }
        else
        {
    	    $ivSize = openssl_cipher_iv_length($method);
    	    $iv = substr($data, 0, $ivSize);
            $data = substr($data, $ivSize);
        }

	    $data = openssl_decrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);

	    return $data;
	}

}