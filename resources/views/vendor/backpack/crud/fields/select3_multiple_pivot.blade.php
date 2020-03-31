<!-- select2 multiple -->
<div class="form-group col-md-12 checklist_dependency_pivot"  data-entity ="{{ $field['name'] }}" @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')
    @php
        $primary_dependency["name"] = $field['name'];
        $secondary_dependency["name"] = "";
    @endphp
    <select
        name="{{ $field['name'] }}[]"
        style="width: 100%"
        @include('crud::inc.field_attributes', ['default_class' =>  'form-control select2_multiple'])
        multiple="multiple">

        @if (isset($field['model']))
          @if(isset($entry))
            @php
                $pivot_entries = $entry->{$field['entity']}->keyBy(function($item) {
                    return $item->getKey();
                });
            @endphp
          @endif
          @php
            if(isset($field['condition_where']))
            {
              if(isset($pivot_entries))
              {
                foreach($pivot_entries as $key => $value)
                {
                  $ids_pivot[] = $key;
                }
              }
              
              if(isset($ids_pivot))
              {
                $fields_ = $field['model']::where($field['condition_where']['name'], $field['condition_where']['operator'], $field['condition_where']['value'])->orWhereIn('id',$ids_pivot)->get();
              }
              else
              {
                $fields_ = $field['model']::where($field['condition_where']['name'], $field['condition_where']['operator'], $field['condition_where']['value'])->get();
              }
            }
            else
            {
              $fields_ = $field['model']::all();
            }
          @endphp

            @foreach ($fields_ as $connected_entity_entry)
                <option value="{{ $connected_entity_entry->getKey() }}"
                    @if ( (isset($field['value']) && in_array($connected_entity_entry->getKey(), $field['value']->pluck($connected_entity_entry->getKeyName(), $connected_entity_entry->getKeyName())->toArray())) || ( old( $field["name"] ) && in_array($connected_entity_entry->getKey(), old( $field["name"])) ) )
                         selected
                    @endif
                >{{ $connected_entity_entry->{$field['attribute']} }}</option>

            @endforeach
        @endif
    </select>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif

    @if(isset($field['pivotFields']) && isset($fields_))
    @foreach ($fields_ as $connected_entity_entry)
        <div class="container-fluid" id="modal_pivotFields_{{$field['name']}}_{{ $connected_entity_entry->getKey() }}" style="display: none">
            @foreach(array_chunk($field['pivotFields'], 2, true) as $pivot_chunk)
                <div class="row">
                @foreach ($pivot_chunk as $pivot_field => $pivot_name)
                  
                    @php
                      $pivot_attr = null;
                      if(isset($pivot_entries))
                      {
                        if ($pivot_entries->has($connected_entity_entry->getKey())) {
                            $pivot = $pivot_entries->get($connected_entity_entry->getKey())->pivot;
                            $pivot_attr = $pivot->getAttribute($pivot_field);
                        }
                      }
                    @endphp
                  
                    <div style="width: 100%" class="col-sm-6">
                        <label style="width: 100%">{!! $pivot_name !!} -> <br>({{ $connected_entity_entry->{$field['attribute']} }})</label><input type="text" name="{!! $pivot_field !!}[{{ $connected_entity_entry->getKey() }}]" value="{{ $pivot_attr or null }}" @include('crud::inc.field_attributes') />
                        <input type="text" name="old_{!! $pivot_field !!}[{{ $connected_entity_entry->getKey() }}]" value="{{ $pivot_attr or null }}" @include('crud::inc.field_attributes') style="display: none"/>
                    </div>
                @endforeach
                </div>
            @endforeach
        </div>
    @endforeach
    @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->checkIfFieldIsFirstOfItsType($field, $fields))

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
        <!-- include select2 css-->
        <link href="{{ asset('vendor/adminlte/plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="https://cdnjs.cloudfla{{ $field['name'] }}[]re.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <!-- include duallistbox js-->
        <script src="{{ asset('js/duallistbox.js') }}"></script>
        <script>
            var demo1 = $("select[name=\"{{ $field['name'] }}[]\"]").bootstrapDualListbox();
        </script>
        <script>
      jQuery(document).ready(function($) {
        $('.checklist_dependency_pivot').each(function(index, item) {
          var unique_name = $(this).data('entity');
          var dependencyJson = window[unique_name];
          var selectedElements = 0;
          var thisField = $(this);
         
          thisField.find('select[name="{{ $primary_dependency["name"] }}[]_helper2"]').trigger('change');

          thisField.find('select[name="{{ $primary_dependency["name"] }}[]_helper2"]').change(function () {       
            thisField.find('select[name="{{ $primary_dependency["name"] }}[]_helper1"] option').each(function(index, item) {
              var idCurrent = $(this).val();
              thisField.find('.hidden_fields_primary > .primary_hidden').each(function(){
                var value = $(this).attr('value');
                if (this.value == idCurrent) {
                  $(this).remove();
                  //thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent).css("display","none");

                }
              });
            });
          });

          
          thisField.find('.hidden_fields_primary').next().find('.removeall').on('click', function() {
            thisField.find('select[name="{{ $primary_dependency["name"] }}[]_helper1"] option').each(function(index, item) {
              var idCurrent = $(this).val();
              thisField.find('.hidden_fields_primary > .primary_hidden').each(function(){
                var value = $(this).attr('value');
                if (this.value == idCurrent) {
                  $(this).remove(); 
                  //thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent).css("display","none");
                }
              });
            });
          });
          


          thisField.find('select[name="{{ $primary_dependency["name"] }}[]"]').change(function() { 
            thisField.find('option').each(function() {              
              var dataSortindex = $(this).attr('data-sortindex');
              
              if(typeof dataSortindex != "undefined")
              {
                var idCurrent = $(this).val();
                //add hidden field with this value

                var exists = false; 
                thisField.find('.hidden_fields_primary > .primary_hidden').each(function(){
                  var value = $(this).attr('value');
                  if (this.value == idCurrent) {
                    exists = true;
                  }
                });
                if (!exists)
                {
                  var nameInput = thisField.find('.hidden_fields_primary').data('name');
                  var inputToAdd = $('<input type="hidden" class="primary_hidden" name="'+nameInput+'[]" value="'+idCurrent+'">');

                  thisField.find('.hidden_fields_primary').append(inputToAdd);  
                  thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent).css("display","inline");

                  //----------------------------------------
                  @if(isset($field['check_value']) && $field['check_value'])

                    //name_text = thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent +' input').attr("name");

                    thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent +' input').filter(':visible:first').keyup(function () {
                      value_remaining_ = thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent +' label').html();
                      value_remaining = value_remaining_.substring(value_remaining_.indexOf(":")+2, value_remaining_.length-1);
                      old_value = thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent +' input').filter(':hidden:first').val();
                      label = value_remaining_.substring(16, value_remaining_.indexOf(":")-17);

                      if($(this).val()-old_value>value_remaining)
                        alert('Error invalid Input - '+label);
                    });
                  @endif
                  //----------------------------------------

                }
              }
            });          
          });

          thisField.find('select[name="{{ $primary_dependency["name"] }}[]_helper2"]').change(function () {

                $('select[name="{{ $primary_dependency["name"] }}[]"] option').each(function() {
                    var idCurrent = $(this).val();
                    if($(this).is(':selected'))
                    {
                        thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent).css("display","inline");
                    }
                    else
                    {
                        thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent).css("display","none");
                        thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent +' input').val("");
                    }
                    
                });
           });
        });

        function formatString(s, args) {
          return s.replace(/\{(\d+)\}/g, function(match, number) {
            return typeof args[number] !== 'undefined' ? args[number] : match;
          });
        }
//Sergio
//alert('ok');
 //alert('{{ $primary_dependency["name"] }}[]');
        $('.checklist_dependency_pivot').each(function(index, item) {
          var unique_name = $(this).data('entity');
          var dependencyJson = window[unique_name];
          var selectedElements = 0;
          var thisField = $(this);

         //console.log(thisField)
          if(thisField.find('select[name="{{ $primary_dependency["name"] }}[]"] option').size()>0) {
                thisField.find('select[name="{{ $primary_dependency["name"] }}[]"] option:selected').each(function() {
                var idCurrent = $(this).val();

                $(this).attr('selected','selected');

                thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent).css("display","inline");
                      
                 @if(isset($field['check_value']) && $field['check_value'])

                  //name_text = thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent +' input').attr("name");

                  thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent +' input').filter(':visible:first').keyup(function () {
                    value_remaining_ = thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent +' label').html();
                    value_remaining = value_remaining_.substring(value_remaining_.indexOf(":")+2, value_remaining_.length-1);                    
                    old_value = thisField.find('#modal_pivotFields_'+unique_name+'_'+idCurrent +' input').filter(':hidden:first').val();
                    label = value_remaining_.substring(16, value_remaining_.indexOf(":")-17);
                    if($(this).val()-old_value>value_remaining)
                      alert('Error invalid Input - '+label);
                  });
                @endif

                }); 
            };
        });

      });
    </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
