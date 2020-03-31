@if (isset($crud->sub_entry_) && isset($crud->sub_entry_['crud']) &&
isset($crud->sub_entry_['sub_crud_relations']) &&
isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]) &&
isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']) &&
$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']->getCrud($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array], $crud->sub_entry_['crud']['name_route'], $crud->sub_entry_['crud']['relation_id'])->hasAccess('sub_update'))
	@php
        $name = $crud->entity_name;
        $route_name = substr($crud->route, strpos($crud->route, '/')+1, strlen($crud->route)-strpos($crud->route, '/')-1);
    @endphp
		<!-- Single edit button -->
		<button type="button"
	            class="btn btn-xs btn-default"
	            data-toggle="modal"
	            data-target="#{{$route_name}}_update_modal_{{$entry->getKey()}}"
	            data-title="{{ trans('backpack::crud.edit') }} {{$name}}"
	            data-message="show_{{$route_name}}_modal_update_message"
	            data-button-type="edit">
	      <i class="fa fa-edit"></i> {{ trans('backpack::crud.edit') }}
	    </button>
    	@include('admin/modal_sub_entry_update', ['fields' => $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']::getSubFieldsCreateUpdate_($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array], $crud->sub_entry_['crud']['relation_id'])->getUpdateFields($entry->getKey()), 'entry_id' => $entry->getKey()])
@elseif($crud->hasAccess('update') && !isset($crud->sub_entry_))
	@if (!$crud->model->translationEnabled())
		<!-- Single edit button -->
		<a href="{{ url($crud->route.'/'.$entry->getKey().'/edit') }}" class="btn btn-xs btn-default"><i class="fa fa-edit"></i> {{ trans('backpack::crud.edit') }}</a>
    @else
		<!-- Edit button group -->
		<div class="btn-group">
		  <a href="{{ url($crud->route.'/'.$entry->getKey().'/edit') }}" class="btn btn-xs btn-default"><i class="fa fa-edit"></i> {{ trans('backpack::crud.edit') }}</a>
		  <button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		    <span class="caret"></span>
		    <span class="sr-only">Toggle Dropdown</span>
		  </button>
		  <ul class="dropdown-menu dropdown-menu-right">
	  	    <li class="dropdown-header">{{ trans('backpack::crud.edit_translations') }}:</li>
		  	@foreach ($crud->model->getAvailableLocales() as $key => $locale)
			  	<li><a href="{{ url($crud->route.'/'.$entry->getKey().'/edit') }}?locale={{ $key }}">{{ $locale }}</a></li>
		  	@endforeach
		  </ul>
		</div>
	@endif
@endif