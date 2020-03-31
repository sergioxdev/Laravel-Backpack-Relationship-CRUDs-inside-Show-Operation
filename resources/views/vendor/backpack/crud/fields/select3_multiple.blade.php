<!-- select3 multiple -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')
    @php
        $primary_key = App::make($field['model'])->getKeyName();
        $field_value = isset($field['value']) ? $field['value']->pluck($primary_key,$primary_key)->toArray() : NULL;
    @endphp
    <select
        name="{{ $field['name'] }}[]"
        style="width: 100%"
        @include('crud::inc.field_attributes', ['default_class' =>  'form-control select2_multiple'])
        multiple="multiple">
        @if (isset($field['model']))
            @foreach ($field['model']::all()->pluck($field['attribute'], $primary_key) as $key =>  $connected_entity_entry)
                <option value="{{ $key }}"
                    @if ( (is_null(old($field["name"])) && isset($field['value']) && in_array($key, $field_value)) || ( old( $field["name"] ) && in_array($key, old( $field["name"])) ) )
                         selected
                    @endif
                >{{ $connected_entity_entry}}</option>
            @endforeach
        @endif
    </select>
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
            var primary_select = $("select[name=\"{{ $field['name'] }}[]\"]").bootstrapDualListbox();
        </script>
    @endpush

@endif
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
