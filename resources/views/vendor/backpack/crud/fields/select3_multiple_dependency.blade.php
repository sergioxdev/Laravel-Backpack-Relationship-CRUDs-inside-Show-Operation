<!-- dependencyJson -->
<div class="form-group col-md-12 checklist_dependency"  data-entity ="{{ $field['field_unique_name'] }}" @include('crud::inc.field_wrapper_attributes')>
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')
    <?php
        $entity_model = $crud->getModel();

        //short name for dependency fields
        $primary_dependency = $field['subfields']['primary'];
        $secondary_dependency = $field['subfields']['secondary'];

        //all items with relation
        $dependencies = $primary_dependency['model']::with($primary_dependency['entity_secondary'])->get();

        $dependencyArray = [];

        //convert dependency array to simple matrix ( prymary id as key and array with secondaries id )
        foreach ($dependencies as $primary) {
            $dependencyArray[$primary->id] = [];
            foreach ($primary->{$primary_dependency['entity_secondary']} as $secondary) {
                $dependencyArray[$primary->id][] = $secondary->id;
            }
        }

      //for update form, get initial state of the entity
      if( isset($id) && $id ){

        //get entity with relations for primary dependency
        $entity_dependencies = $entity_model->with($primary_dependency['entity'])
          ->with($primary_dependency['entity'].'.'.$primary_dependency['entity_secondary'])
          ->find($id);

            $secondaries_from_primary = [];

            //convert relation in array
            $primary_array = $entity_dependencies->{$primary_dependency['entity']}->toArray();

            $secondary_ids = [];

            //create secondary dependency from primary relation, used to check what chekbox must be check from second checklist
            if (old($primary_dependency['name'])) {
                foreach (old($primary_dependency['name']) as $primary_item) {
                    foreach ($dependencyArray[$primary_item] as $second_item) {
                        $secondary_ids[$second_item] = $second_item;
                    }
                }
            } else { //create dependecies from relation if not from validate error
                foreach ($primary_array as $primary_item) {
                    foreach ($primary_item[$secondary_dependency['entity']] as $second_item) {
                        $secondary_ids[$second_item['id']] = $second_item['id'];
                    }
                }
            }
        }

        //json encode of dependency matrix
        $dependencyJson = json_encode($dependencyArray);
    ?>
    <script>
        var  {{ $field['field_unique_name'] }} = {!! $dependencyJson !!};
    </script>

    <div class="row" >

        <div class="col-xs-12">
            <label>{!! $primary_dependency['label'] !!}</label>
        </div>

        @php
            $primary_dependency_key = App::make($primary_dependency['model'])->getKeyName();
            $primary_dependency_field_value = isset($field['value']) ? $field['value'][0]->pluck($primary_dependency_key,$primary_dependency_key)->toArray() : NULL;            
        @endphp
        <div class="hidden_fields_primary" data-name = "{{ $primary_dependency['name'] }}">
        @if(isset($field['value']))
            @if(old($primary_dependency['name']))
                @foreach( old($primary_dependency['name']) as $item )
                <input type="hidden" class="primary_hidden" name="{{ $primary_dependency['name'] }}[]" value="{{ $item }}">
                @endforeach
            @else
                @foreach($primary_dependency_field_value as $item )
                <input type="hidden" class="primary_hidden" name="{{ $primary_dependency['name'] }}[]" value="{{ $item }}">
                @endforeach
            @endif
        @endif
        </div>

       <select
        name="{{ $primary_dependency['name'] }}_show[]"
        style="width: 80%"
        @include('crud::inc.field_attributes', ['default_class' =>  'form-control select2_multiple primary_list'])
        multiple="multiple"
        @foreach ($primary_dependency as $attribute => $value)
            @if (is_string($attribute) && $attribute != 'value')
                @if ($attribute=='name')
                {{ $attribute }}="{{ $value }}_show[]"
                @else
                {{ $attribute }}="{{ $value }}"
                @endif
            @endif
        @endforeach
        >
        @foreach ($primary_dependency['model']::all()->pluck($primary_dependency['attribute'], $primary_dependency_key) as $key =>  $connected_entity_entry)
              <option value="{{ $key }}"
                  @if( ( isset($field['value']) && in_array($key, $primary_dependency_field_value))
                  || ( old($primary_dependency["name"]) && in_array($key, old( $primary_dependency["name"])) ) )
                  selected
                  @endif >
                  {{ $connected_entity_entry }}</option>
        @endforeach
        </select>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <label>{!! $secondary_dependency['label'] !!}</label>
        </div>
        @php
            $secondary_dependency_key = App::make($secondary_dependency['model'])->getKeyName();
            $secondary_dependency_field_value = isset($field['value']) ? $field['value'][1]->pluck($secondary_dependency_key,$secondary_dependency_key)->toArray() : NULL;            
        @endphp
        <div class="hidden_fields_secondary" data-name="{{ $secondary_dependency['name'] }}">
          @if(isset($field['value']))
            @if(old($secondary_dependency['name']))
              @foreach( old($secondary_dependency['name']) as $item )
                <input type="hidden" class="secondary_hidden" name="{{ $secondary_dependency['name'] }}[]" value="{{ $item }}">
              @endforeach
            @else
              @foreach($secondary_dependency_field_value as $item )
                <input type="hidden" class="secondary_hidden" name="{{ $secondary_dependency['name'] }}[]" value="{{ $item }}">
              @endforeach
            @endif
          @endif
        </div>

        <select
        name="{{ $secondary_dependency['name'] }}_show[]"
        style="width: 80%"
        @include('crud::inc.field_attributes', ['default_class' =>  'form-control select2_multiple secondary_list'])
        multiple="multiple"
        @foreach ($secondary_dependency as $attribute => $value)
            @if (is_string($attribute) && $attribute != 'value')
                @if ($attribute=='name')
                {{ $attribute }}="{{ $value }}_show[]"
                @else
                {{ $attribute }}="{{ $value }}"
                @endif
            @endif
        @endforeach
        >
        @foreach ($secondary_dependency['model']::all()->pluck($secondary_dependency['attribute'], $secondary_dependency_key) as $key =>  $connected_entity_entry)
          <option class = 'secondary_list' data-id = "{{ $key }}"
          value="{{ $key }}"
                @if( ( isset($field['value']) && in_array($key, $secondary_dependency_field_value))
                || isset( $secondary_ids[$key]) || ( old($secondary_dependency["name"]) && in_array($key, old( $secondary_dependency["name"])) ) )
                selected
                @if(isset( $secondary_ids[$key]))
                  disabled = "disabled"
                 @endif
                @endif >
                {{ $connected_entity_entry }}</option>
        @endforeach
        </select>
    </div>

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
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <!-- include duallistbox js-->
        <script src="{{ asset('js/duallistbox.js') }}"></script>
        <script>
            var demo1 = $("select[name=\"{{ $field['name'] }}[]\"]").bootstrapDualListbox();
        </script>
    <!-- include checklist_dependency js-->
    <script>
      jQuery(document).ready(function($) {
        $('.checklist_dependency').each(function(index, item) {
          var unique_name = $(this).data('entity');
          var dependencyJson = window[unique_name];
          var selectedElements = 0;
          var thisField = $(this);


          thisField.find('select[name="{{ $primary_dependency["name"] }}_show[]_helper2"]').change(function () {            
            thisField.find('select[name="{{ $primary_dependency["name"] }}_show[]_helper1"] option').each(function(index, item) {
              var idCurrent = $(this).val();
              thisField.find('.hidden_fields_primary > .primary_hidden').each(function(){
                var value = $(this).attr('value');
                if (this.value == idCurrent) {
                  $(this).remove();
                  deleteSecondary(thisField, idCurrent, dependencyJson);
                }
              });
            });
          });

          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper1"]').change(function () {
            upgradeSecondary(thisField, dependencyJson);
          });

          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper2"]').change(function () {
            upgradeSecondary(thisField, dependencyJson);
          });

          thisField.find('.hidden_fields_primary').next().find('.removeall').on('click', function() {
            thisField.find('select[name="{{ $primary_dependency["name"] }}_show[]_helper1"] option').each(function(index, item) {
              var idCurrent = $(this).val();
              thisField.find('.hidden_fields_primary > .primary_hidden').each(function(){
                var value = $(this).attr('value');
                if (this.value == idCurrent) {
                  $(this).remove();       
                  deleteSecondary(thisField, idCurrent, dependencyJson);
                }
              });
            });
          });

          thisField.find('.hidden_fields_secondary').next().find('.removeall').on('click', function() {
            upgradeSecondary(thisField, dependencyJson);
          });
          
          thisField.find('.hidden_fields_secondary').next().find('.clear1').on('click', function() {
            selectedElements = 0;
            thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"] option').each(function(index, item) {
              var $item = $(item);
              var dataSortindex = $(this).attr('data-sortindex');
              if ($item.prop('selected') || $item.is(':selected') || $item.attr( "selected" )=='selected' || typeof dataSortindex != "undefined"){
                selectedElements++;
              }
            });

            thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"]').trigger('change');

            filterSecondary(thisField, dependencyJson, selectedElements, 'clear1');
          });

          thisField.find('.hidden_fields_secondary').next().find('.clear2').on('click', function() {
            selectedElements = 0;
            thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"] option').each(function(index, item) {
              var $item = $(item);
              var dataSortindex = $(this).attr('data-sortindex');
              if ($item.prop('selected') || $item.is(':selected') || $item.attr( "selected" )=='selected' || typeof dataSortindex != "undefined"){
                selectedElements++;
              }
            });

            thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"]').trigger('change');

            filterSecondary(thisField, dependencyJson, selectedElements, 'clear2');
          });


          thisField.find('select[name="{{ $primary_dependency["name"] }}_show[]"]').change(function() { 
            thisField.find('.primary_list option').each(function() {              
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
                  insertSecondary(thisField, idCurrent, dependencyJson);
                }
              }
            });          
          });

          thisField.find('.secondary_list').change(function() { 
            thisField.find('.secondary_list option').each(function() {              
              var dataSortindex = $(this).attr('data-sortindex');             
              if(typeof dataSortindex != "undefined")
              {
                var idCurrent = $(this).val();
                //add hidden field with this value
                var exists = false; 
                thisField.find('.hidden_fields_secondary > .secondary_hidden').each(function(){
                  var value = $(this).attr('value');
                  if (this.value == idCurrent) {
                    exists = true;
                  }
                });
                if (!exists)
                {
                  var nameInput = thisField.find('.hidden_fields_secondary').data('name');
                  var inputToAdd = $('<input type="hidden" class="secondary_hidden" name="'+nameInput+'[]" value="'+idCurrent+'">');

                  thisField.find('.hidden_fields_secondary').append(inputToAdd);
                }
              }
            });          
          });

          /*
          thisField.find('.secondary_list').click(function() {

            var idCurrent = $(this).data('id');
            if($(this).is(':selected')){
              alert(0);
              //add hidden field with this value
              var nameInput = thisField.find('.hidden_fields_secondary').data('name');
              var inputToAdd = $('<input type="hidden" class="secondary_hidden" name="'+nameInput+'[]" value="'+idCurrent+'">');

              thisField.find('.hidden_fields_secondary').append(inputToAdd);

            }else{
              alert(1);
              //remove hidden field with this value
              thisField.find('.hidden_fields_secondary > .secondary_hidden[value="'+idCurrent+'"]').remove();
            }
          });
          */
        });

        function insertSecondary(thisField, idCurrent, dependencyJson) {

          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper1"]').empty();
          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper2"]').empty();
          selectedElements = 0;
          $.each(dependencyJson[idCurrent], function(key, value){
            thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"] > .secondary_list[value="'+value+'"]').attr("selected", true);
            thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"] > .secondary_list[value="'+value+'"]').attr( "disabled", true );
            //remove hidden fields with secondary dependency if was setted
            var hidden = thisField.find('secondary_hidden[value="'+value+'"]');
            if(hidden)
              hidden.remove();
          });

          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"]  > .secondary_list').each(function(index, item)
          {
            var $item = $(item);
            if ($item.prop('selected') || $item.is(':selected') || $item.attr( "selected" )=='selected') {
              selectedElements++;
              thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper2"]').append($item.clone(true).prop('selected', $item.data('_selected')));
            } else {
              thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper1"]').append($item.clone(true).prop('selected', $item.data('_selected')));
            }
          });

          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"]').trigger('change');
          filterSecondary(thisField, dependencyJson, selectedElements);
        }

        function deleteSecondary(thisField, idCurrent, dependencyJson) {

          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper1"]').empty();
          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper2"]').empty();
          selectedElements = 0;
          var selected = [];
          thisField.find('.hidden_fields_primary > .primary_hidden').each(function (index, input){
            selected.push( $(this).val() );
          });

          $.each(dependencyJson[idCurrent], function(index, value){
            var ok = 1;

            $.each(selected, function(index2, selectedItem){
              if( dependencyJson[selectedItem].indexOf(value) != -1 ){
                ok =0;
              }
            });

            if(ok){
              thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"] > .secondary_list[value="'+value+'"]').removeAttr("selected");
              thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"] > .secondary_list[value="'+value+'"]').removeAttr("disabled");
            }
          });

          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"] option').each(function(index, item) {
            var $item = $(item);
            if ($item.prop('selected') || $item.is(':selected') || $item.attr( "selected" )=='selected'){
              selectedElements++;
              thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper2"]').append($item.clone(true).prop('selected', $item.data('_selected')));
            } else {
              thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper1"]').append($item.clone(true).prop('selected', $item.data('_selected')));
            }
          });
          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"]').trigger('change');

          filterSecondary(thisField, dependencyJson, selectedElements);
        }

        function filterSecondary(thisField, dependencyJson, selectedElements, type_filter=null) {

          var visible1 = thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper1"] option').length,
            visible2 = thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper2"] option').length,
            all1 = thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"] option').length - selectedElements,
            all2 = selectedElements,
            content = '1';
          //alert("selectedElements: "+selectedElements+" visible1: "+visible1+" visible2: "+visible2+" all1: "+all1+" all2: "+all2);
          if(type_filter=='clear1')
          {
            if (all1 === 0) {
              content = "Empty list";
            } else if (visible1 === all1) {
              content = formatString('Showing all {0}', [visible1, all1]);
            } else {
              content = formatString('<span class="label label-warning">Filtered</span> {0} from {1}', [visible1, all1]);
            }
            thisField.find('.hidden_fields_secondary').next().find('.info').first().html(content);
            thisField.find('.hidden_fields_secondary').next().find('.box1').toggleClass('filtered', !(visible1 === all1 || all1 === 0));
          }
          else if (type_filter=='clear2')
          {
            if (all2 === 0) {
              content = "Empty list";
            } else if (visible2 === all2) {
              content = formatString('Showing all {0}', [visible2, all2]);
            } else {
              content = formatString('<span class="label label-warning">Filtered</span> {0} from {1}', [visible2, all2]);
            }
            thisField.find('.hidden_fields_secondary').next().find('.info').last().html(content);
            thisField.find('.hidden_fields_secondary').next().find('.box2').toggleClass('filtered', !(visible2 === all2 || all2 === 0));
          }
          else 
          {
            if (all1 === 0) {
              content = "Empty list";
            } else if (visible1 === all1) {
              content = formatString('Showing all {0}', [visible1, all1]);
            } else {
              content = formatString('<span class="label label-warning">Filtered</span> {0} from {1}', [visible1, all1]);
            }
            thisField.find('.hidden_fields_secondary').next().find('.info').first().html(content);
            thisField.find('.hidden_fields_secondary').next().find('.box1').toggleClass('filtered', !(visible1 === all1 || all1 === 0));

            if (all2 === 0) {
              content = "Empty list";
            } else if (visible2 === all2) {
              content = formatString('Showing all {0}', [visible2, all2]);
            } else {
              content = formatString('<span class="label label-warning">Filtered</span> {0} from {1}', [visible2, all2]);
            }
            thisField.find('.hidden_fields_secondary').next().find('.info').last().html(content);
            thisField.find('.hidden_fields_secondary').next().find('.box2').toggleClass('filtered', !(visible2 === all2 || all2 === 0));
          }
        }

        function upgradeSecondary(thisField, dependencyJson)         {
          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper1"]').empty();
          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper2"]').empty();
          selectedElements = 0;

          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"] option').each(function(index, item) {
            var $item = $(item);

            if ($item.prop('selected') || $item.is(':selected') || $item.attr( "selected" )=='selected' || typeof dataSortindex != "undefined"){
              selectedElements++;
              thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper2"]').append($item.clone(true).prop('selected', $item.data('_selected')));
            } else {
              thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]_helper1"]').append($item.clone(true).prop('selected', $item.data('_selected')));
              thisField.find('.hidden_fields_secondary > .secondary_hidden').each(function(){
                var value = $(this).attr('value');
                if (this.value == $item.prop('value')) {
                  $(this).remove();
                }
              });
            }
          });
          thisField.find('select[name="{{ $secondary_dependency["name"] }}_show[]"]').trigger('change');
          filterSecondary(thisField, dependencyJson, selectedElements);
        }

        function formatString(s, args) {
          return s.replace(/\{(\d+)\}/g, function(match, number) {
            return typeof args[number] !== 'undefined' ? args[number] : match;
          });
        }

      });
    </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}


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
            var primary_select_dependency = $("select[name=\"{{ $primary_dependency['name'] }}_show[]\"]").bootstrapDualListbox();
            var secondary_select_dependency = $("select[name=\"{{ $secondary_dependency['name'] }}_show[]\"]").bootstrapDualListbox();
        </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
