@extends('backpack::layout')

@section('content-header')
	<section class="content-header">
	  <h1>
        <span class="text-capitalize">{{ $crud->entity_name_plural }}</span>
        <small>{{ ucfirst(trans('backpack::crud.preview')).' '.$crud->entity_name }}.</small>
      </h1>
	  <ol class="breadcrumb">
	    <li><a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a></li>
	    <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
	    <li class="active">{{ trans('backpack::crud.preview') }}</li>
	  </ol>
	</section>
@endsection

@section('content')
<div class="row">
	<div style="margin:auto; width: 90%">
		@if ($crud->hasAccess('list'))
		<a href="{{ url($crud->route) }}"><i class="fa fa-angle-double-left"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a><br><br>
		@endif
		
		@include('crud::inc.grouped_errors')

	<!-- Default box -->
	  <div id="box_show" class="box" style="padding: 0 10px 0 10px;">
	    <div class="box-header with-border">
	      <h3 class="box-title">
            {{ trans('backpack::crud.preview') }}
            <span>{{ $crud->entity_name }}</span>
          </h3>
          <div class="col-xs-6" style="float:right;">
            @php
              $crud_b = clone $crud; 
              unset($crud_b->sub_entry_);
              $crud_b->removeButton("show");
            @endphp
 

            @if ($crud_b->buttons->where('stack', 'line')->count() )
            <table id="crudTable" style="float: right">
              <tr>
                <td>
                  <div id="bottom_buttons" class="hidden-print" style="float:right;">
                    @include('crud::inc.button_stack', ['stack' => 'line', 'crud' => $crud_b, "show" => true])

                    <div id="datatable_button_stack" class="pull-right text-right hidden-xs"></div>
                  </div>
                </td>
              </tr>
              </table>
            @endif
          </div>
          
	    </div>

	    <div class="box-body">
	    	@if ($crud->hasAccess('show'))
          
		      	<!-- load the view from the application if it exists, otherwise load the one in the package -->
			    @include('admin/show_content')
			    @php
				    $cruds_array = array();			    
				    $entries_array = array();
				    $crud->cruds_name_array = array();
			    @endphp
          @if(isset($crud->sub_entry_))
  			    @foreach ($crud->sub_entry_['sub_crud_relations'] as $key => $sub_crud_)
              @php
                $getCrud = $sub_crud_['controller_new']->getCrud($sub_crud_, $crud->sub_entry_['crud']['name_route'], $crud->sub_entry_['crud']['relation_id']);
                
                /*
      					//$cruds_array[$key] = $sub_crud_['controller_new']->getCrud($sub_crud_, $crud->sub_entry_['crud']['name_route'], $crud->sub_entry_['crud']['relation_id']);
                */				
      					$cruds_array[$key] = $getCrud;
                /*
                //$entries_array[$key] = $sub_crud_['controller_new']->getCrud($sub_crud_, $crud->sub_entry_['crud']['name_route'], $crud->sub_entry_['crud']['relation_id'])->getEntries();
                */
                $entries_array[$key] = $getCrud->getEntries();

      					$crud->cruds_name_array[$key] = $key;
      				@endphp
  				  @endforeach
          @endif
          @if(sizeof($cruds_array)>0)
            @include('admin/show_sub_content_script')
  				  @foreach ($cruds_array as $key => $crud_)
            @php
            //var_dump($crud->sub_entry_['crud']);
            //var_dump($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array[$key]])
            $crud_->cruds_name_array = $crud->cruds_name_array[$key];
            $crud_->sub_entry_ = $crud->sub_entry_;
            @endphp
  			    	@include('admin/show_sub_content', ['crud' => $crud_, 'entries' => $entries_array[$key], 'cruds_name_array' => $crud->cruds_name_array[$key], 'crud_base' => $crud, 'entry_crud' => $crud->sub_entry_['crud']])
  			    @endforeach
          @endif
		    @endif
	    </div><!-- /.box-body -->
	  </div><!-- /.box --> 
	</div>
</div>
@endsection

@section('after_styles')
	<!-- DATA TABLES -->
  	<link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
  	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap.min.css">	

	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/show.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/form.css') }}">
	<link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/list.css') }}">
  @stack('crud_list_styles')
	<style type="text/css">    
    #sub_content_show .form-group {
      display: table !important;
    }
    #sub_content_show .form-group.has-error .help-block {
        color: #dd4b39 !important;
    }
    #sub_content_show .form-group.has-error label {
        color: #dd4b39 !important;
    }

    #sub_content_show #crudTable_length > label {
      width: 100% !important;
      display: contents !important;
    }
    #sub_content_show #crudTable_filter > label {
      display: contents !important;
    }
    #sub_content_show .dataTables_filter {
      float: right !important;;
    }
    
    .modal .col-md-12 {
        border-color: #ddd;
        border-radius: 5px;
        border-style: solid;
        border-width: 1px 1px 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        margin: 20px;
        overflow: hidden;
    }

    .modal .col-md-12 {
        border-bottom: 1px solid #dddddd;
        margin: 0px 0px 0px 20px;
        width: 90%;
        padding-left: 0;
    }

    .modal .col-md-12 label {
        background-color: #f4f4f4;
        color: #333;
        display: table-cell;
        font-weight: bold;
        height: 30px;
        padding: 5px 10px;
        vertical-align: middle;
        width: 150px;
    }

    .modal .col-md-12 span {
        display: table-cell;
        padding: 5px 15px;
        vertical-align: middle;
    }
    
    .modal .modal-body
    {
      overflow-y: scroll;
      height: 300px;
      scrollbar-width: none; /* Firefox */
      -ms-overflow-style: none;  /* IE 10+ */
      &::-webkit-scrollbar {
        width: 0px;
        background: transparent; /* Chrome/Safari/Webkit */
      }
    }

    .modal .modal-body .bootstrap-datetimepicker-widget.dropdown-menu {
      position: fixed;
    }
    
    .modal .modal-body .form-control {
      width: auto !important;;
    }
    .modal .modal-body .input-group {
      display: block !important;
    }
    .modal .modal-body .help-block {
      display: grid !important;;
    }
    .modal .modal-footer {
        padding-top: 0px !important;
        clear: both !important;
    }
    .modal .modal-content .input-group-addon {
        width: auto;
    }
    .btn {
      padding: 5px 10px 5px 10px;
      font-size: inherit;
    }
	</style>
  <!-- include select2 css -->
  <link href="{{ asset('vendor/adminlte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('after_scripts')
  <script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
  <script src="{{ asset('vendor/backpack/crud/js/form.js') }}"></script>
  <script src="{{ asset('vendor/backpack/crud/js/list.js') }}"></script>
  <script src="{{ asset('vendor/backpack/crud/js/create.js') }}"></script>
  <script src="{{ asset('vendor/backpack/crud/js/edit.js') }}"></script>
  <!-- DATA TABLES SCRIPT -->
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap.min.js"></script>
	<div id="_"></div>

  <!-- include select2 js-->
  <script src="{{ asset('vendor/adminlte/bower_components/select2/dist/js/select2.min.js') }}"></script>
@endsection