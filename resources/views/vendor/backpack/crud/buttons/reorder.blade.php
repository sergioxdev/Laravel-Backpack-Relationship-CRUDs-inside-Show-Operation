@if (isset($crud->sub_entry_) && isset($crud->sub_entry_['crud']) &&
isset($crud->sub_entry_['sub_crud_relations']) &&
isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]) &&
isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']) &&
$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']->getCrud($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array], $crud->sub_entry_['crud']['name_route'], $crud->sub_entry_['crud']['relation_id'])->hasAccess('sub_reorder') && isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder']) && isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder']['attribute_name']) && isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder']['depth']))

	  <a href="{{ url($crud->route.'/reorder2/'.$entry->getTable().'/'.$entry->getKey().'/'.$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder']['attribute_name'].'/'.$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder']['depth']) }}" class="btn btn-default ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-arrows"></i> {{ trans('backpack::crud.reorder') }} {{ $crud->entity_name_plural }}</span></a>

@elseif($crud->hasAccess('reorder') && !isset($crud->sub_entry_))
	@if ($crud->reorder)
		@if ($crud->hasAccess('reorder'))
		  <a href="{{ url($crud->route.'/reorder') }}" class="btn btn-default ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-arrows"></i> {{ trans('backpack::crud.reorder') }} {{ $crud->entity_name_plural }}</span></a>
		@endif
	@endif
@endif


