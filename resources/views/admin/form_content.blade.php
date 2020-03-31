@if ($crud->model->translationEnabled())
<input type="hidden" name="locale" value={{ $crud->request->input('locale')?$crud->request->input('locale'):App::getLocale() }}>
@endif

{{-- See if we're using tabs --}}
@if ($crud->tabsEnabled())
    @include('crud::inc.show_tabbed_fields')
@else
    @include('crud::inc.show_fields', ['fields' => $fields])
@endif

{{-- Define blade stacks so css and js can be pushed from the fields to these sections. --}}
@push('crud_fields_scripts_')
<div>
    <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/'.$action.'.css') }}">

    <!-- CRUD FORM CONTENT - crud_fields_styles stack -->
    @stack('crud_fields_styles')

    <script src="{{ asset('vendor/backpack/crud/js/'.$action.'.js') }}"></script>

    <!-- CRUD FORM CONTENT - crud_fields_scripts stack -->
    
    @stack('crud_fields_scripts')   
</div>

@endpush