@if (isset($crud->sub_entry_) && isset($crud->sub_entry_['crud']) &&
isset($crud->sub_entry_['sub_crud_relations']) &&
isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]) &&
isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']) &&
$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']->getCrud($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array], $crud->sub_entry_['crud']['name_route'], $crud->sub_entry_['crud']['relation_id'])->hasAccess('sub_create'))
	@php
        $name = $crud->entity_name;
        $route_name = substr($crud->route, strpos($crud->route, '/')+1, strlen($crud->route)-strpos($crud->route, '/')-1);
    @endphp
    @if($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_type']=='belongsToMany' || $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_type']=='belongsToManyB')
                    
        <button type="button"
                class="btn btn-primary ladda-button"
                data-toggle="modal"
                data-target="#{{$crud_base->entity_name}}_update_modal_{{$entry->getKey()}}_{{ $route_name}}"
                data-title="{{ trans('backpack::crud.edit') }} {{$name}}"
                data-message="show_{{$route_name}}_modal_update_message"
                data-style="zomm-in"
                data-button-type="edit">
            <span class="ladda-label"><i class="fa fa-edit"></i> {{ trans('backpack::crud.edit') }} {{$name}}</span>
        </button>        
        @include('admin/modal_sub_entry_update', ['fields' => $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['controller_new']::getSubFieldsCreateUpdate_($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array], $crud->sub_entry_['crud']['relation_id'], $crud->sub_entry_['crud']['controller_new'])->getUpdateFields($entry->getKey()), 'entry_id' => $entry->getKey(), 'crud' => $crud_base, 'sub_crud_up' => $crud])
    @else            
        <button type="button"
            class="btn btn-primary ladda-button"
            data-toggle="modal"
            data-target="#{{$route_name}}_create_modal"
            data-title="{{ trans('backpack::crud.add_a_new') }} {{$name}}"
            data-message="show_{{$route_name}}_modal_create_message"
            data-style="zomm-in">
        <span class="ladda-label"><i class="fa fa-plus"></i> {{ trans('backpack::crud.add') }} {{$name}}</span>
        </button>
        {{-- FIELD JS - will be loaded in the after_scripts section --}}
        @push('after_scripts')
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('[data-target~="#{{$route_name}}_create_modal"]').click(function() {
                    //console.log("kkkkk");
                    var form_ = $('#{{$route_name}}_create_modal_form');
                    //form_.get(0).reset(); 
                    form_.find('input:text, input:password, input:file, select, textarea').val('');
                    form_.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
                    //console.log(form_.find('.hidden input:hidden').val());
                    form_.find('.hidden input:hidden').val('');

                    var er_ = form_.find('.form-group.has-error');
                    er_.each(function() {
                        $(this).removeClass('has-error');
                        $(this).find('.help-block').remove();
                    });
                    form_.find('.callout.callout-danger li').remove();
                    form_.find('.callout.callout-danger').css('display','none');

                    form_.find("select[class*='select2']").select2();

                    var sel_ = form_.find("select[class*='select2']");

                    sel_.each(function() {
                        if($(this).attr('data-type')!="")
                        {
                            $(this).find('option:not(:first)').remove();
                            $(this).parent("div").find("span:first").addClass("select2-container--disabled");
                        }    
                    });
                });
            });              
        </script>
        @endpush        
        @include('admin/modal_sub_entry_create', ['entry_id' => $entry->getKey()])
    @endif
@elseif($crud->hasAccess('create') && !isset($crud->sub_entry_))
    <a href="{{ url($crud->route.'/create') }}" class="btn btn-primary ladda-button" data-style="zoom-in"><span class="ladda-label"><i class="fa fa-plus"></i> {{ trans('backpack::crud.add') }} {{ $crud->entity_name }}</span></a>
@endif