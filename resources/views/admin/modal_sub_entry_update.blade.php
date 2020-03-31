@php
$sub_crud = isset($sub_crud_up) ? $sub_crud_up : '';
$sub_crud_name = $sub_crud !== '' ? '_'.$sub_crud->entity_name : '';
$title = $sub_crud !== '' ? $sub_crud->entity_name : $crud->entity_name;
@endphp

<!-- Modal Dialog -->
<div class="modal fade" id="{{$route_name}}_update_modal_{{$entry->getKey()}}{{$sub_crud_name}}" role="dialog" aria-labelledby="{{$route_name}}_update_modal_label" aria-hidden="true" style="overflow: unset">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    @php
    $check_file = false;
    if ($crud->hasUploadFields('create'))
      $check_file = true;
    else
      $check_file = false;
    @endphp
    {!! Form::open(array(
      'route' => 'crud.'.$route_name.'.update2', 'style' => 'display:inline', 
      'form_id' => $route_name.'_update_modal_'.$entry->getKey().$sub_crud_name,
      'id' => $route_name.'_update_modal_'.$entry->getKey().$sub_crud_name, 
      'files' => $check_file)) !!}
    {!! Form::hidden($crud->sub_entry_['crud']['name_route'].'s_id', $crud->sub_entry_['crud']['relation_id']) !!}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">{{ trans('backpack::crud.edit') }} {{$title}}</h4>
        </div>
        <div class="modal-body" data-simplebar>
          @include('admin/grouped_errors')
          <!-- load the view from the application if it exists, otherwise load the one in the package -->
               @include('admin/form_content', [ 'fields' => $fields, 'action' => 'edit' ]) 
        </div>
        <div class="modal-footer" style="clear: both;">
            {!! Form::hidden('id', $entry->getKey()) !!}
            {!! Form::submit('btn-cancel', ['class' => 'btn btn-default','data-dismiss' => 'modal']) !!}
            {!! Form::submit('btn-submit', ['class' => 'btn btn-primary', 'data-button-type' => 'update']) !!}
        </div>
    {!! Form::close() !!}
    </div>
  </div>
</div>
