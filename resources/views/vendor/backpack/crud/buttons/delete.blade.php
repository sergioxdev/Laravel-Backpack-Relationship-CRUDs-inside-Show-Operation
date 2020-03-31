@if (isset($crud->sub_entry_) &&
isset($crud->sub_entry_['sub_crud_relations']) &&
isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]) &&
isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']) &&
$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']->getCrud($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array], $crud->sub_entry_['crud']['name_route'], $crud->sub_entry_['crud']['relation_id'])->hasAccess('sub_delete'))
	@php
        $name = $crud->entity_name;
        $route_name = substr($crud->route, strpos($crud->route, '/')+1, strlen($crud->route)-strpos($crud->route, '/')-1);
    @endphp

	<a href="javascript:void(0)" onclick="{{$route_name}}_subdeleteEntry(this)" data-route="{{ url($crud->route.'/'.$entry->getKey()) }}" class="btn btn-xs btn-default" data-button-type="sub_delete"><i class="fa fa-trash"></i> {{ trans('backpack::crud.delete') }}</a>

	@include('admin/modal_sub_entry_delete')
		
@elseif($crud->hasAccess('delete') && !isset($crud->sub_entry_))
	<a href="javascript:void(0)" onclick="deleteEntry(this)" data-route="{{ url($crud->route.'/'.$entry->getKey()) }}" class="btn btn-xs btn-default" data-button-type="delete"><i class="fa fa-trash"></i> {{ trans('backpack::crud.delete') }}</a>
	<script>
	if (typeof deleteEntry != 'function') {
		@if(!isset($show))
	  	$("[data-button-type=delete]").unbind('click');
	  	@endif

		function deleteEntry(button) 
		{
			// ask for confirmation before deleting an item
			// e.preventDefault();
			var button = $(button);
			var route = button.attr('data-route');			
			//console.log(button);
			var div_id = button.parents('table').attr('id');
			var div_id_name = div_id.substring(0,div_id.length);
			var row = $("#"+div_id_name+" a[data-route='"+route+"']").parentsUntil('tr').parent();

			//console.log(div_id_name);
	      	if (confirm("{{ trans('backpack::crud.delete_confirm') }}") == true) 						
	      	{
				$.ajax({
					url: route,
					type: 'DELETE',
					success: function(result) {
					  // Show an alert with the result
					  new PNotify({
					      title: "{{ trans('backpack::crud.delete_confirmation_title') }}",
					      text: "{{ trans('backpack::crud.delete_confirmation_message') }}",
					      type: "success"
					  });

					  // Hide the modal, if any
					  $('.modal').modal('hide');

					  // Remove the row from the datatable
					  row.remove();
					  @if(isset($show) && $show = true)
					  	window.location.replace("{{ url($crud->route)}}");
					  @else
					  	crud.table.ajax.reload();
					  @endif
					},
					error: function(result) {
					  // Show an alert with the result
					  new PNotify({
					      title: "{{ trans('backpack::crud.delete_confirmation_not_title') }}",
					      text: "{{ trans('backpack::crud.delete_confirmation_not_message') }}",
					      type: "warning"
					  });
					}
				});
			} else {
				  // Show an alert telling the user we don't know what went wrong
			  	new PNotify({
			      title: "{{ trans('backpack::crud.delete_confirmation_not_deleted_title') }}",
			      text: "{{ trans('backpack::crud.delete_confirmation_not_deleted_message') }}",
			      type: "info"
			  	});
			}
		}	
	}

	// make it so that the function above is run after each DataTable draw event
	// crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
</script>
@endif