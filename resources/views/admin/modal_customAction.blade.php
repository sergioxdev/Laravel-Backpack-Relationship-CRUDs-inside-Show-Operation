@if($sub==true)
  @php
  $sub_crud = isset($sub_crud_up) ? $sub_crud_up : '';
  $sub_crud_name = $sub_crud !== '' ? '_'.$sub_crud->entity_name : '';
  $title = $sub_crud !== '' ? $sub_crud->entity_name : $crud->entity_name;
  @endphp

  <!-- Modal Dialog -->
  <div class="modal fade" id="{{$crud->entity_name}}_custom_action_modal_{{$entry->getKey()}}{{$sub_crud_name}}" role="dialog" aria-labelledby="{{$crud->entity_name}}_custom_action_modal_label" aria-hidden="true">
    <div class="modal-dialog" style="width: 45% !important;">
      <div class="modal-content">
      @php
      $check_file = false;
      if ($crud->hasUploadFields('create'))
        $check_file = true;
      else
        $check_file = false;
      @endphp
      {!! Form::open(array(
        'route' => 'crud.'.$crud->entity_name.'.'.$action_name, 'style' => 'display:inline', 
        'form_id' => $crud->entity_name.'_custom_action_modal_'.$entry->getKey().$sub_crud_name,
        'id' => $crud->entity_name.'_custom_action_modal_'.$entry->getKey().$sub_crud_name, 
        'files' => $check_file)) !!}
      {!! Form::hidden($crud->sub_entry_['crud']['name_route'].'s_id', $crud->sub_entry_['crud']['relation_id']) !!}
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">flash-x</button>
            <h4 class="modal-title">{{ ucfirst($action_name) }} {{$title}}</h4>
          </div>
          @include('admin/grouped_errors')
          <div class="modal-body">
            <!-- load the view from the application if it exists, otherwise load the one in the package -->
            @include('admin/modal_customActionForm_'.$form_name, [])
               
          </div>
          <div class="modal-footer">
              {!! Form::hidden('id', $entry->getKey()) !!}
              {!! Form::submit('btn-cancel', ['class' => 'btn btn-default','data-dismiss' => 'modal']) !!}
              {!! Form::submit('btn-submit', ['class' => 'btn btn-primary', 'data-button-type' => 'custom_action']) !!}
          </div>
      {!! Form::close() !!}
      </div>
    </div>
  </div>
@else
<!-- Modal Dialog -->
<div class="modal fade" id="{{$crud->entity_name}}_cust_action_modal_{{$entry->getKey()}}" role="dialog" aria-labelledby="{{$crud->entity_name}}_cust_action_modal_label" aria-hidden="true">
  <div class="modal-dialog" style="width: 45% !important;">
    <div class="modal-content">
    {!! Form::open(array('route' => 'crud.'.$crud->entity_name.'.'.$action_name, 'style' => 'display:inline', 'form_id' => $crud->entity_name.'_cust_action_modal_'.$entry->getKey())) !!}

    {!! Form::hidden('id', $entry->getKey()) !!}
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">flash-x</button>
          <h4 class="modal-title">{{ ucfirst($action_name) }} {{$crud->entity_name}}</h4>
        </div>
        @include('admin/grouped_errors')
        <div class="modal-body">
          <!-- load the view from the application if it exists, otherwise load the one in the package -->
            @include('admin/modal_customActionForm_'.$form_name, [])
        </div>
        <div class="modal-footer">
            {!! Form::submit('btn-cancel', ['class' => 'btn btn-default','data-dismiss' => 'modal']) !!}
            {!! Form::submit('btn-submit', ['class' => 'btn btn-primary', 'data-button-type' => 'cust_action']) !!}
        </div>
    {!! Form::close() !!}
    </div>
  </div>
</div>
@endif
