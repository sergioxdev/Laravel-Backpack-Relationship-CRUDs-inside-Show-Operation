<script>
  if (typeof {{$route_name}}_subdeleteEntry != 'function') {
    $("[data-button-type=sub_delete]").unbind('click');

    function {{$route_name}}_subdeleteEntry(button) 
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

            @if($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']->getCrud($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array], $crud->sub_entry_['crud']['name_route'], $crud->sub_entry_['crud']['relation_id'])->hasAccess('sub_delete'))

              crud_{!! $route_name !!}.table.ajax.reload();
            
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
</script>