<!-- dependencyJson -->
<div class="form-group col-xs-12 select3_dependency"  data-entity-select3="{{ $field['field_unique_name'] }}" @include('crud::inc.field_wrapper_attributes') style="width: 100%">
  <label>{!! $field['label'] !!}</label><br>
  @include('crud::inc.field_translatable_icon')
  @foreach ($field['subfields'] as $key_dependency => $fields) 
    @php
      $fields_label = $fields['label'];
      $fields_name = $fields['name'];
      $fields_entity = $fields['entity'];
      $fields_previous = isset($fields['entity_previous']) ? $fields['entity_previous'] : NULL;
      $fields_next = isset($fields['entity_next']) ? $fields['entity_next'] : NULL;
      $fields_attribute = $fields['attribute'];
    @endphp
    <div style="float: left; width: 100%; margin-bottom: 20px;" data-gr-select="{{ $fields_entity }}">
    <div style="float: left">
    
    <label>{{ $fields_label }}:</label>
    </div>
    <div style="float: left; width:60% !important">
      <!--class="input-group" data-bs-select="{{ $fields_entity }}">-->

    <select style="width:90% !important" name="{{ $fields_entity }}s"  data-type="{{$fields_previous}}" id="select3_{{ $fields_entity }}s_list" @include('crud::inc.field_attributes', ['default_class' =>  'form-control select3_field'])>
      <option value disabled selected>Select {{ $fields_label }}</option>
    </select>  
    </div>
    </div>
  @endforeach

  @push('crud_fields_scripts')
    <script>
    $(document).ready(function()
    {
      $('[data-entity-select3="{{$field['name']}}"]').each(function() 
      {
          var $fake = $(this);
          var $fake_form = $fake.parents('form:first');
      
      @foreach ($field['subfields'] as $key_dependency => $fields) 
        @php
          $fields_label = $fields['label'];
          $fields_name = $fields['name'];
          $fields_entity = $fields['entity'];
          $fields_previous = isset($fields['entity_previous']) ? $fields['entity_previous'] : NULL;
          $fields_next = isset($fields['entity_next']) ? $fields['entity_next'] : NULL;
          $fields_attribute = $fields['attribute'];
        @endphp


        var input_val = $fake_form.find('#input_{{ $fields_entity }}s_id').val();

        @if($fields_previous!==NULL)
         $fake.find('#select3_{{ $fields_entity }}s_list').prop('disabled', true);
          if (input_val !== "" && input_val !== "0" )
          {          
            var input_previous_val = $fake_form.find('#input_{{ $fields_previous }}s_id').val();
            get{{ ucfirst(strtolower($fields_entity)) }}s(input_previous_val,$fake);
          }
        @else
          get{{ ucfirst(strtolower($fields_entity)) }}s('0',$fake);
        @endif

        $fake.find('#select3_{{ $fields_entity }}s_list').change(function() 
        {
          $fake_form.find('#input_{{ $fields_entity }}s_id').val("");
          $fake_form.find('#input_{{ $fields_entity }}s_id').val(this.value);

          @if($fields_next!==NULL)
            $fake_form.find('#input_{{ $fields_next }}s_id').val("");

            $fake.find('#select3_{{ $fields_next }}s_list').prop('disabled', true);
              $fake.find('#select3_{{ $fields_next }}s_list').empty().append('<option value disabled selected>Select {{ ucfirst($fields_next)}}</option>');
            if(this.value!=="")
            {

              get{{ucfirst($fields_next)}}s(this.value,$fake);
            }          
            $fake.find('#select3_{{ $fields_next }}s_list').trigger("change");
          @endif
        });
      @endforeach
      });
    });
    
    @foreach ($field['subfields'] as $key_dependency => $fields) 
      @php
        $fields_label = $fields['label'];
        $fields_name = $fields['name'];
        $fields_entity = $fields['entity'];
        $fields_previous = isset($fields['entity_previous']) ? $fields['entity_previous'] : NULL;
        $fields_next = isset($fields['entity_next']) ? $fields['entity_next'] : NULL;
        $fields_attribute = $fields['attribute'];
      @endphp
      function get{{ ucfirst($fields_entity) }}s(val,fake)
      {
        $.ajax({
          type: 'post',
          dataType: 'json',

          @if($fields_previous!==NULL)
            url: '{{url(str_replace(str_replace(" ", "_", $crud->entity_name),"",$crud->route))}}/{{strtolower($fields_entity)}}/get{{ $fields_entity }}s',
          data:'id='+val,
          @else
            url: '{{url(str_replace(str_replace(" ", "_", $crud->entity_name),"",$crud->route))}}/{{strtolower($fields_entity)}}/get{{strtolower($fields_entity)}}s',
          @endif          
          success: function(data)
          {
            var json_obj = $.parseJSON(data);
            var fields_entity = "{{ $fields_entity }}";
            var fields_entity_ = "{{ ucfirst($fields_entity)}}";
            var fields_next = "{{ ucfirst($fields_next)}}";
            var fake_form = fake.parents('form:first');
            var $field = fake.find('select[id="select3_'+fields_entity+'s_list"]');
            var $input = fake_form.find('input[id="input_'+fields_entity+'s_id"]');

            $field.empty().append('<option value disabled selected>Select '+fields_entity_+'</option>');
            if(json_obj.length>0)
            {
              $.each(json_obj, function(key, value) {
                $field.append('<option value="'+value.id+'" selected="">'+value.{{$fields_attribute}}+'</option>');
              });
              $field.removeAttr("disabled");
            }

            fake.find('#select3_'+fields_entity+'s_list').val(fake.find('#select3_'+fields_entity+'s_list option[value="Select '+fields_entity_+'"]').val());

            var input_val = $input.val();
            if (input_val !== "" && input_val !== "0" ) 
            {
              fake.find('#select3_'+fields_entity+'s_list').val(fake.find('#select3_'+fields_entity+'s_list option[value="'+input_val+'"]').val());
            }
          }
        });
      }
    @endforeach
    </script>

    {{-- ########################################## --}}
    {{-- Extra CSS and JS for this particular field --}}
    {{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
    @if ($crud->checkIfFieldIsFirstOfItsType($field))

        {{-- FIELD CSS - will be loaded in the after_styles section --}}
        @push('crud_fields_styles')
            <!-- include select2 css-->
            <link href="{{ asset('vendor/adminlte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
            <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        @endpush

        {{-- FIELD JS - will be loaded in the after_scripts section --}}
        @push('crud_fields_scripts')
            <!-- include select2 js-->
            <script src="{{ asset('vendor/adminlte/bower_components/select2/dist/js/select2.min.js') }}"></script>
            <script>
              
                jQuery(document).ready(function($) {
                    // trigger select2 for each untriggered select2 box
                    $('.select3_field').each(function (i, obj) {
                        if (!$(obj).hasClass("select2-hidden-accessible"))
                        {
                            $(obj).select2({
                                theme: "bootstrap"
                            });
                        }
                    });
                });
            </script>
        @endpush

    @endif
    {{-- End of Extra CSS and JS --}}
    {{-- ########################################## --}}
    @endpush

</div>