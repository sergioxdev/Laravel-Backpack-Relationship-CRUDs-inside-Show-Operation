<!-- select -->

<div @include('crud::inc.field_wrapper_attributes') >

    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')

    <?php $entity_model = $crud->getRelationModel($field['entity'],  - 1); 

    if(isset($crud_base) && isset($crud_base->entry->id))
        $id = $crud_base->entry->id;
    elseif(isset($crud) && isset($crud->entry->id))
        $id = $crud->entry->id;
    elseif(isset($crud->sub_entry_['crud']['name_route']) && isset($crud->sub_entry_['crud']['relation_id']))
        $id = $crud->sub_entry_['crud']['relation_id'];
    else
        $id = null;
    ?>

    <select
        name="{{ $field['name'] }}"
        @include('crud::inc.field_attributes')
        >

        @if ($entity_model::isColumnNullable($field['name']))
            <option value="">-</option>
        @endif

        @if (isset($field['model']))
            @php
            if(isset($field['function']))
                $fields_ = $field['model']::{$field['function']}($id);
            else
                $fields_ = $field['model']::all();
            @endphp

            @foreach ($fields_ as $connected_entity_entry)
                @if(old($field['name']) == $connected_entity_entry->getKey() || (is_null(old($field['name'])) && isset($field['value']) && $field['value'] == $connected_entity_entry->getKey()))
                    <option value="{{ $connected_entity_entry->getKey() }}" selected>{{ $connected_entity_entry->{$field['attribute']} }}</option>
                @else
                    <option value="{{ $connected_entity_entry->getKey() }}">{{ $connected_entity_entry->{$field['attribute']} }}</option>
                @endif
            @endforeach
        @endif
    </select>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif

</div>