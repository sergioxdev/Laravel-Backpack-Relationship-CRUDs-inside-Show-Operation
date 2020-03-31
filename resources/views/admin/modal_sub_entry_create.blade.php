<!-- Modal Dialog -->
<div class="modal fade" id="{{$route_name}}_create_modal" role="dialog" aria-labelledby="{{$route_name}}_create_modal_label" aria-hidden="true" style="overflow: unset;">
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
      'route' => 'crud.'.$route_name.'.store2', 'style' => 'display:inline', 
      'form_id' => "{$route_name}_create_modal_form", 
      'id' => "{$route_name}_create_modal_form", 
      'files' => $check_file)) !!}
        {!! Form::hidden($crud->sub_entry_['crud']['name_route'].'s_id', $entry_id ) !!}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">{{ trans('backpack::crud.add_a_new') }} {{$name}}</h4>
        </div>
        <div class="modal-body" data-simplebar>
          @include('admin/grouped_errors')
          <!-- load the view from the application if it exists, otherwise load the one in the package -->
            @include('admin/form_content', [ 'fields' => $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']::getSubFieldsCreateUpdate_($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array], $crud->sub_entry_['crud']['relation_id'])->getFields('create'), 'action' => 'create' ])
        </div>        
        <div class="modal-footer" style="clear: both;">
            {!! Form::submit('btn-cancel', ['class' => 'btn btn-default','data-dismiss' => 'modal']) !!}
            {!! Form::submit('btn-submit', ['class' => 'btn btn-primary', 'data-button-type' => 'create']) !!}
        </div>
    {!! Form::close() !!}
    </div>
  </div>
</div>