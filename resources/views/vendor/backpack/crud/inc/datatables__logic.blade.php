<?php  use App\User; ?>
<script>
  var crud_{!! $crud->cruds_name_array !!} = {
    exportButtons: JSON.parse('{!! json_encode($crud->export_buttons) !!}'),
    functionsToRunOnDataTablesDrawEvent: [],
    addFunctionToDataTablesDrawEventQueue: function (functionName) {
        if (this.functionsToRunOnDataTablesDrawEvent.indexOf(functionName) == -1) {
        this.functionsToRunOnDataTablesDrawEvent.push(functionName);
      }
    },
    responsiveToggle(dt) {
        $(dt.table().header()).find('th').toggleClass('all');
        dt.responsive.rebuild();
        dt.responsive.recalc();
    },
    executeFunctionByName: function(str, args) {
      var arr = str.split('.');
      var fn = window[ arr[0] ];

      for (var i = 1; i < arr.length; i++)
      { fn = fn[ arr[i] ]; }
      fn.apply(window, args);
    },
    dataTableConfiguration: {
      responsive: {
          details: {
              display: $.fn.dataTable.Responsive.display.modal( {
                  header: function ( row ) {
                      var data = row.data();
                      return data[0];
                  }
              } ),
              renderer: function ( api, rowIdx, columns ) {
                var data = $.map( columns, function ( col, i ) {
                    return '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                              '<td><strong>'+col.title.trim()+':'+'<strong></td> '+
                              '<td>'+col.data+'</td>'+
                            '</tr>';
                } ).join('');

                return data ?
                    $('<table class="table table-striped table-condensed m-b-0">').append( data ) :
                    false;
              },
          }
      },
      autoWidth: false,
      pageLength: {{ $crud->getDefaultPageLength() }},
      lengthMenu: @json($crud->getPageLengthMenu()),
      /* Disable initial sort */
      aaSorting: [],
      language: {
            "emptyTable":     "{{ trans('backpack::crud.emptyTable') }}",
            "info":           "{{ trans('backpack::crud.info') }}",
            "infoEmpty":      "{{ trans('backpack::crud.infoEmpty') }}",
            "infoFiltered":   "{{ trans('backpack::crud.infoFiltered') }}",
            "infoPostFix":    "{{ trans('backpack::crud.infoPostFix') }}",
            "thousands":      "{{ trans('backpack::crud.thousands') }}",
            "lengthMenu":     "{{ trans('backpack::crud.lengthMenu') }}",
            "loadingRecords": "{{ trans('backpack::crud.loadingRecords') }}",
            "processing":     "<img src='{{ asset('vendor/backpack/crud/img/ajax-loader.gif') }}' alt='{{ trans('backpack::crud.processing') }}'>",
            "search":         "{{ trans('backpack::crud.search') }}",
            "zeroRecords":    "{{ trans('backpack::crud.zeroRecords') }}",
            "paginate": {
                "first":      "{{ trans('backpack::crud.paginate.first') }}",
                "last":       "{{ trans('backpack::crud.paginate.last') }}",
                "next":       "<span class='hidden-xs hidden-sm'>{{ trans('backpack::crud.paginate.next') }}</span><span class='hidden-md hidden-lg'>></span>",
                "previous":   "<span class='hidden-xs hidden-sm'>{{ trans('backpack::crud.paginate.previous') }}</span><span class='hidden-md hidden-lg'><</span>"
            },
            "aria": {
                "sortAscending":  "{{ trans('backpack::crud.aria.sortAscending') }}",
                "sortDescending": "{{ trans('backpack::crud.aria.sortDescending') }}"
            },
            "buttons": {
                "copy":   "{{ trans('backpack::crud.export.copy') }}",
                "excel":  "{{ trans('backpack::crud.export.excel') }}",
                "csv":    "{{ trans('backpack::crud.export.csv') }}",
                "pdf":    "{{ trans('backpack::crud.export.pdf') }}",
                "print":  "{{ trans('backpack::crud.export.print') }}",
                "colvis": "{{ trans('backpack::crud.export.column_visibility') }}"
            },
        },
        processing: true,
        serverSide: true,
        ajax: {
            "url": "{!! url($crud->route.'/search').'?'.Request::getQueryString() !!}",
            "type": "POST",
            data: function(d) {
              //d.search_mod = "true",
              

              @php
                //---------------------------------------------------------------
                $data_json_ = [
                  'search_mod' => true,
                ];
                if(isset($crud->sub_entry_['crud']['name_route'])) {
                  //echo "d.crud_name_route = \"".$crud->sub_entry_['crud']['name_route']."\";\n";
                  $data_json_['crud_name_route'] = $crud->sub_entry_['crud']['name_route'];
                }
                if(isset($crud->sub_entry_['crud']['relation_id'])) {
                  //echo "d.crud_relation_id = \"".$crud->sub_entry_['crud']['relation_id']."\";\n";
                  $data_json_['crud_relation_id'] = $crud->sub_entry_['crud']['relation_id'];
                }
                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['name_route'])) {
                  //echo "d.name_route = \"".$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['name_route']."\";\n";
                  $data_json_['sub_name_route'] = $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['name_route'];
                }
                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_type'])) {
                  //echo "d.relation_type = \"".$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_type']."\";\n";
                  $data_json_['sub_relation_type'] = $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_type'];
                }
                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_name'])) {
                  //echo "d.relation_name = \"".$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_name']."\";\n";
                  $data_json_['sub_relation_name'] = $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_name'];
                }
                
                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_id'])) {
                  //echo "d.sub_relation_id = \"".$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_id']."\";\n";
                  $data_json_['sub_relation_id'] = $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['relation_id'];
                }

                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['fields'])) {
                  //echo "d.fields = [];";
                  //echo "obj = [];";
                  //echo "obj = {};";
                  $obj = [];
                  foreach ($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['fields'] as $key => $column) {                     
                    //echo "subobj = [];";                    
                    $subobj = [];
                    foreach ($column as $key_ => $column_) {
                      //echo "subobj.push('".$column_."');";
                      $subobj[$key_] = $column_;
                    }
                    //echo "obj.push({'".$key."':subobj});";      
                    //echo "obj['".$key."'] = subobj;";     
                    $obj[$key] = $subobj;
                  }
                  //echo "obj.".$key." = [];";
                  //  echo "obj['".$key."'].push({subobj});";  

                  //echo "d.fields = [obj];";
                  $data_json_['fields'] = $obj;
                }

                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['fields_pivot'])) {
                  //echo "obj = {};";
                  $obj = [];
                  foreach ($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['fields_pivot'] as $key => $column) {                     
                    //echo "subobj = [];";
                    //echo "subobj_ = {};";
                    $subobj = [];
                    $subobj_ = [];
                    foreach ($column as $key_ => $column_) {
                      if(is_array($column_))
                      {
                        foreach ($column_ as $key__ => $column__) {
                          //echo "subobj.push('".$column__."');";
                          $subobj[] = $column__;
                        }
                        //echo "subobj_.".$key_." = subobj;";
                        $subobj_[$key_] = $subobj;
                      }
                      else 
                      {
                        //echo "subobj.push({'".$key_."' :'".$column_."'});";
                        //echo "subobj_.".$key_." = ['".$column_."'];";
                        $subobj_[$key_] = $column_;
                      }                      
                    }
                    //echo "subobj_['".$key_."'] = subobj;";
                    //echo "obj.push({'".$key."':subobj});";      
                    
                    //echo "obj.".$key." = [subobj_];";   
                    $obj[$key] = $subobj_;   
                    //echo "obj['".$key."'] = subobj;";      
                  }
                  //echo "obj.".$key." = [];";
                  //  echo "obj['".$key."'].push({subobj});";  

                  //echo "d.fields_pivot = [obj];";
                  $data_json_['fields_pivot'] = $obj;
                }
                
                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['show_relations'])) 
                {
                  $obj = [];
                  foreach ($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['show_relations'] as $key => $column) 
                  {
                    $subobj = [];
                    $subobj_ = [];
                    foreach ($column as $key_ => $column_) {
                      if(is_array($column_))
                      {
                        foreach ($column_ as $key__ => $column__) 
                        {
                          $subobj[] = $column__;
                        }
                        $subobj_[$key_] = $subobj;
                      }
                      else 
                      {
                        $subobj_[$key_] = $column_;
                      }                      
                    }  
                    $obj[$key] = $subobj_;     
                  }
                  $data_json_['show_relations'] = $obj;
                }

                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder'])) {
                  
                  if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder']['attribute_name']))
                    $data_json_['enable_reorder']['attribute_name'] = $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder']['attribute_name'];

                  if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder']['depth']))
                    $data_json_['enable_reorder']['depth'] = $crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['enable_reorder']['depth'];
                }

                /*
                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['permissions'])) {
                  //echo "d.fields = [];";
                  //echo "obj = [];";
                  //echo "obj = {};";
                  $obj = [];
                  foreach ($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['permissions'] as $key => $column) {                     
                    //echo "subobj = [];";
                    $subobj = [];
                    foreach ($column as $key_ => $column_) {
                      //echo "subobj.push('".$column_."');";
                      $subobj[] = $column_;
                    }
                    //echo "obj.push({'".$key."':subobj});";      
                    //echo "obj['".$key."'] = subobj;";
                    $obj[$key] = $subobj;    
                  }
                  //echo "obj.".$key." = [];";
                  //  echo "obj['".$key."'].push({subobj});";      
                  
                  //echo "d.permissions = [obj];";
                  $data_json_['permissions'] = $obj;
                }
                */

                //------------------------------------
                $data_json_ = json_encode($data_json_);
                $data_json_encrypted = User::open_encrypt($data_json_);

                if(isset($data_json_encrypted)) {
                  echo "d.data_json_encrypted = \"".$data_json_encrypted."\";\n";
                }                


                if(isset($crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['link'])) {     
                  echo "d.link = ".$crud->sub_entry_['sub_crud_relations'][$crud->cruds_name_array]['link'].";";
                }
                //---------------------------------------------------------------
                @endphp
            }
        },
        dom:
          "<'row'<'col-sm-6 hidden-xs'l><'col-sm-6'f>>" +
          "<'row'<'col-sm-12'tr>>" +
          "<'row'<'col-sm-5'i><'col-sm-2'B><'col-sm-5'p>>",

      drawCallback: function( settings ) {
        load_data_js('{!! $crud->cruds_name_array !!}');
      }
    }
  }
</script>

@include('crud::inc.export_buttons')

<script type="text/javascript">
  jQuery(document).ready(function($) {
    crud_{!! $crud->cruds_name_array !!}.table = $("#crudTable_{!! $crud->cruds_name_array !!}").DataTable(crud_{!! $crud->cruds_name_array !!}.dataTableConfiguration);
    // override ajax error message
    $.fn.dataTable.ext.errMode = 'none';
    $('#crudTable_{!! $crud->cruds_name_array !!}').on('error.dt', function(e, settings, techNote, message) {
        new PNotify({
            type: "error",
            title: "{{ trans('backpack::crud.ajax_error_title') }}",
            text: "{{ trans('backpack::crud.ajax_error_text') }}"
        });
    });

    // make sure AJAX requests include XSRF token
    $.ajaxPrefilter(function(options, originalOptions, xhr) {
        var token = $('meta[name="csrf_token"]').attr('content');

        if (token) {
              return xhr.setRequestHeader('X-XSRF-TOKEN', token);
        }
    });

    // on DataTable draw event run all functions in the queue
    // (eg. delete and details_row buttons add functions to this queue)
    $('#crudTable_{!! $crud->cruds_name_array !!}').on( 'draw.dt',   function () {
      register_create_button_action_('{!! $crud->cruds_name_array !!}');
      register_update_button_action_('{!! $crud->cruds_name_array !!}');
      register_custom_button_action_('{!! $crud->cruds_name_array !!}');
       crud_{!! $crud->cruds_name_array !!}.functionsToRunOnDataTablesDrawEvent.forEach(function(functionName) {
          crud_{!! $crud->cruds_name_array !!}.executeFunctionByName(functionName);
       });
    } ).dataTable();

    // when columns are hidden by reponsive plugin,
    // the table should have the has-hidden-columns class
    crud_{!! $crud->cruds_name_array !!}.table.on( 'responsive-resize', function ( e, datatable, columns ) {
        if (crud_{!! $crud->cruds_name_array !!}.table.responsive.hasHidden()) {
          $("#crudTable_{!! $crud->cruds_name_array !!}").removeClass('has-hidden-columns').addClass('has-hidden-columns');
         } else {
          $("#crudTable_{!! $crud->cruds_name_array !!}").removeClass('has-hidden-columns');
         }
    } );
  });

  //----------------------------------------------------------
  //----------------------------------------------------------
  
</script>
@include('crud::inc.details_row__logic')