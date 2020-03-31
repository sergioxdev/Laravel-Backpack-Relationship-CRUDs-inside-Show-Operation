<!-- Default box -->
  <div id="sub_content_show" class="crudTable_{{$crud->cruds_name_array}}_row">
    <!-- THE ACTUAL CONTENT -->
    <div class="col-md-12" style="margin:auto; width: 100%">	
      <div class="box">
        <div class="box-header {{ $crud->hasAccess('create')?'with-border':'' }}">
    		  <h4>
    			 <span class="text-capitalize">{{ $crud->entity_name_plural }}</span>
    		  </h4>
          @include('crud::inc.button_stack', ['stack' => 'top'])
          <div id="datatable_button_stack" class="pull-right text-right hidden-xs"></div>
        </div>

        <div class="box-body table-responsive">

          {{-- Backpack List Filters --}}
          @if ($crud->filtersEnabled())
            @include('crud::inc.filters_navbar')
          @endif

          <table id="crudTable_{{$crud->cruds_name_array}}" class="table table-striped table-hover display responsive nowrap" cellspacing="0">
            <thead>
              <tr>
                {{-- Table columns --}}
                @foreach ($crud->columns as $column)
                  <th {{ isset($column['orderable']) ? 'data-orderable=' .var_export($column['orderable'], true) : '' }}>
                    {{ $column['label'] }}
                  </th>
                @endforeach

                @if ( $crud->buttons->where('stack', 'line')->count() )
                  <th data-orderable="false">{{ trans('backpack::crud.actions') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>

              @if (!$crud->ajaxTable())
                @foreach ($entries as $k => $entry)
                <tr data-entry-id="{{ $entry->getKey() }}">

                  @if ($crud->details_row)
                    @include('crud::columns.details_row_button')
                  @endif

                  {{-- load the view from the application if it exists, otherwise load the one in the package --}}

                  @foreach ($crud->columns as $column)
                    @if (!isset($column['type']))
                      @include('crud::columns.text')
                    @else
                      @if(view()->exists('vendor.backpack.crud.columns.'.$column['type']))
                        @include('vendor.backpack.crud.columns.'.$column['type'])
                      @else
                        @if(view()->exists('crud::columns.'.$column['type']))
                          @include('crud::columns.'.$column['type'])
                        @else
                          @include('crud::columns.text')
                        @endif
                      @endif
                    @endif

                  @endforeach

                  @if ($crud->buttons->where('stack', 'line')->count())
                    <td>
                      @include('crud::inc.button_stack', ['stack' => 'line', 'cruds_name_array' => $crud->cruds_name_array])
                    </td>
                  @endif

                </tr>
                @endforeach
              @endif

            </tbody>
            <tfoot>
              <tr>
                {{-- Table columns --}}
                @foreach ($crud->columns as $column)
                  <th>{{ $column['label'] }}</th>
                @endforeach

                @if ( $crud->buttons->where('stack', 'line')->count() )
                  <th>{{ trans('backpack::crud.actions') }}</th>
                @endif
              </tr>
            </tfoot>
          </table>

        </div><!-- /.box-body -->

        @include('crud::inc.button_stack', ['stack' => 'bottom'])

      </div><!-- /.box -->
    </div>
  </div>

@section('after_scripts')
  @parent
  @include('crud::inc.datatables__logic', ['cruds_name_array' => $crud->cruds_name_array])

  <!-- CRUD LIST CONTENT - crud_list_scripts stack -->
  @stack('crud_list_scripts')

  <!-- CRUD FIELDS CONTENT - crud_fields_scripts stack -->
  @stack('crud_fields_scripts')
@endsection
