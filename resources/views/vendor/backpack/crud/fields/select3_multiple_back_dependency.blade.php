<!-- select multiple -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')
    @php
        $primary_key = App::make($field['model'])->getKeyName();
        $field_value = isset($field['value']) ? $field['value']->pluck($primary_key,$primary_key)->toArray() : NULL;

        //$field['value']->pluck($connected_entity_entry->getKeyName(), $connected_entity_entry->getKeyName())->toArray()
    @endphp
    <select
        class="form-control"
        name="{{ $field['name'] }}[]"
        @include('crud::inc.field_attributes')
        multiple>

        @if (!isset($field['allows_null']) || $field['allows_null'])
            <option value="">-</option>
        @endif

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