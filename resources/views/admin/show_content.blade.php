<div id="content_show" class="divTable" style="width: 100%;">
    <div class="divTableBody">
@php
    $current_fields = $crud->getCurrentFields();
@endphp 
{{-- Show the inputs --}}
@foreach ($entry->getAttributes() as $key => $field)
    @if($key != 'id' && $key != 'created_at' && $key != 'updated_at' && $key != 'deleted_at' && (isset($crud->sub_entry_) && in_array($key, $crud->sub_entry_['crud']['fields']['show'])))

        @if(isset($crud->sub_entry_) && isset($crud->sub_entry_['crud']) && isset($crud->sub_entry_['crud']['fields']) && isset($crud->sub_entry_['crud']['fields']['show']) && in_array($key,$crud->sub_entry_['crud']['fields']['show']))
        @php
            $label = isset($current_fields[$key]) ? ucfirst($current_fields[$key]['label']) : ucfirst(str_replace('_', ' ',str_replace('_id', '',str_replace('s_id', '', $key))));
        @endphp
            @if(!strpos($key,'_id'))
                <div class="divTableRow">
                <div class="divTableCellDesc">
                <label>{{ $label }}: </label>
                </div>
                <div class="divTableCellValue">
                <span>@include('admin/show_field', ['field_name' => $key, 'field' => $field])</span>
                </div>
                </div>
            @else
                <div class="divTableRow">
                <div class="divTableCellDesc">
                <label>{{ $label }}: </label>
                </div>
                <div class="divTableCellValue">
                <span>
                    @php
                        $key_ = str_replace('_id', '',str_replace('s_id', '', $key));
                    @endphp
                    @if(isset($crud->sub_entry_) && isset($crud->sub_entry_['crud']) && isset($crud->sub_entry_['crud']['show_relations']) && array_key_exists($key_, $crud->sub_entry_['crud']['show_relations']))

                        @php
                            $style = '';                            
                        @endphp
                        @if(isset($crud->sub_entry_['crud']['show_relations'][$key_]['uicolor']) && $crud->sub_entry_['crud']['show_relations'][$key_]['uicolor'] && (isset($crud->sub_entry_['crud']['show_relations'][$key_]['entry']) && is_object($crud->sub_entry_['crud']['show_relations'][$key_]['entry']) && $crud->sub_entry_['crud']['show_relations'][$key_]['entry']->first() !== NULL))

                            @php
                                $entry_array = $crud->sub_entry_['crud']['show_relations'][$key_]['entry']->first();
                                $column_name_color = $crud->sub_entry_['crud']['show_relations'][$key_]['uicolor'];
                                $color = $entry_array[$column_name_color];
                                $style = 'style="color: '.$color.';font-weight:bold"';
                            @endphp 
                        @endif 

                        <div {!!$style!!}>
                        @if(isset($crud->sub_entry_['crud']['show_relations'][$key_]['entry']) && is_object($crud->sub_entry_['crud']['show_relations'][$key_]['entry']) && $crud->sub_entry_['crud']['show_relations'][$key_]['entry']->first() !== NULL)
                            @php
                            $entry_array = $crud->sub_entry_['crud']['show_relations'][$key_]['entry']->first();
                            @endphp
                            @if(isset($crud->sub_entry_['crud']['show_relations'][$key_]['link']) && $crud->sub_entry_['crud']['show_relations'][$key_]['link'])      
                                <a href="{!! url(config('backpack.base.route_prefix', 'admin')) !!}/{!! str_replace('s_id', '', $key) !!}/{{$field}}">
                                    @foreach($crud->sub_entry_['crud']['show_relations'][$key_]['columns'] as $column)
                                    @if(strpos($column, '_id') != false)
                                        @php
                                            $point = strpos($column, '.');
                                            $size = strlen($column);
                                            $fname = substr($column, 0, $point);
                                            $lname = substr($column, $point+1, $size);
                                            $Fname = ucfirst(str_replace("s_id", "", $fname));
                                        @endphp
                                        {!! $entry_array->$Fname()->first()->$lname !!}
                                        
                                    @else
                                        {!! $entry_array[$column] !!}
                                    @endif
                                    @endforeach
                                </a>
                            @else
                                @foreach($crud->sub_entry_['crud']['show_relations'][$key_]['columns'] as $column)
                                        {!! $entry_array[$column] !!} 
                                @endforeach
                            @endif
                        @else
                            -
                        @endif
                        </div>
                    @else
                        {{$field}}
                    @endif
                </span>
            </div>
            </div>
            @endif
        @endif
    @endif
@endforeach
</div>
</div>

{{-- FIELD CSS - will be loaded in the after_styles section --}}
@push('after_styles')
<style type="text/css">
    #content_show.box .col-md-12 {
        border-color: #ddd;
        border-radius: 5px;
        border-style: solid;
        border-width: 1px 1px 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        margin: 20px;
        overflow: hidden;
    }

    #content_show.box .col-md-12 {
        border-bottom: 1px solid #dddddd;
        margin: 0px 0px 0px 20px;
        width: 90%;
        padding-left: 0;
    }

    #content_show.box .col-md-12 label {
        background-color: #f4f4f4;
        color: #333;
        display: table-cell;
        font-weight: bold;
        height: 30px;
        padding: 5px 10px;
        vertical-align: middle;
        width: 150px;
    }

    #content_show.box .col-md-12 span {
        display: table-cell;
        padding: 5px 15px;
        vertical-align: middle;
    }

    /*
    .divTable > div:first-child {
        
    }
    */
    /* DivTable.com */
    #content_show.divTable{
        display: table;
        width: 100%;
        border-radius:10px 0px 0px 0px;
        -moz-border-radius:10px 0px 0px 0px;
        -webkit-border-radius:10px 0px 0px 0px;
        overflow: hidden;    
    }
    #content_show .divTableRow {
        display: table-row;
    }
    #content_show .divTableHeading {
        background-color: #EEE;
        display: table-header-group;
    }
    #content_show .divTableCell, #content_show .divTableHead {
        border-width:0px 0px 0px 0px;
        border-style:solid solid dashed solid;
        border-color:#000000 #000000 #000000 #000000;
        display: table-cell;
        padding: 3px 10px;
    }
    #content_show .divTableCellDesc, #content_show .divTableHead {
        border-width:0px 0px 0px 0px;
        border-style:solid solid dashed solid;
        border-color:#000000 #000000 #000000 #000000;
        background-color: #3c8dbc;
        display: table-cell;
        padding: 3px 10px;
        width: 30%;
        text-align: right;
        color: #ffffff;
    }
    #content_show .divTableCellValue, #content_show .divTableHead {
        border-width:0px 0px 1px 0px;
        border-style:solid solid dashed solid;
        border-color:#000000 #000000 #000000 #000000;
        display: table-cell;
        padding: 3px 10px;
        width: 70%;
    }
    #content_show .divTableHeading {
        background-color: #EEE;
        display: table-header-group;
        font-weight: bold;
    }
    #content_show .divTableFoot {
        background-color: #EEE;
        display: table-footer-group;
        font-weight: bold;
    }
    #content_show .divTableBody {
        display: table-row-group;
    }

    #content_show [class^="crudTable_"] .cke span {
        padding: 0px !important;
        
    }
    #content_show [class^="crudTable_"] .cke .cke_button_label, 
    #content_show [class^="crudTable_"] .cke .cke_voice_label {
        display: none !important;
        padding: 0px !important;
        overflow: hidden;
        clear: both;
    }
</style>
@endpush

{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('after_scripts')
<script type="text/javascript">
    jQuery(document).ready(function($)
    {
        $('[id$="_file_download"]').click(function(e)
        {
            //---------------------------------------------------
            var href_url_file = $(this).attr('href');
            var dat = $(this).attr('dat');
            var dlink = $(this).attr('dlink'); 
            href_url_file = href_url_file.replace("#", "");
            
            var data_ = {
                'file_': href_url_file,
                'dat_': dat,
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            };

            //---------------------------------------------------
            $.ajaxSetup({
                headers: {
                    'X-CSRF-Token': "{{ csrf_token() }}"
                }
            });

            @php
                /*
                $permission_ = str_replace("crud.", "", Route::currentRouteName());
                $pos = strpos($permission_, ".");

                if($pos>0)
                    $permission_ = substr($permission_, 0, $pos);

                $route = route("crud.".$permission_.".downloadFile");
                */
            @endphp

            $.ajax({
                type: "POST",
                url: dlink,
                data: data_,
                processData: true,
                xhrFields:{
                    responseType: 'blob'
                },
            }).done(function (response,status,xhr) {
                var filename = "";
                var disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }
                var type = xhr.getResponseHeader('Content-Type');
                
                //console.log('response: %o', response);
                var blob = new Blob([response], { type: type });
                var URL = window.URL || window.webkitURL;
                var downloadUrl = URL.createObjectURL(blob);

                if (filename) {
                    // use HTML5 a[download] attribute to specify filename
                    var a = document.createElement("a");
                    // safari doesn't support this yet
                    if (typeof a.download === 'undefined') {
                        window.location = downloadUrl;
                    } else {
                        a.href = downloadUrl;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                    }
                } else {
                    window.location = downloadUrl;
                }
                setTimeout(function() { URL.revokeObjectURL(downloadUrl); }, 100); // cleanup

            }).fail(function (jqXHR, textStatus, errorThrown) {
                //alert('Error: ' + textStatus);
            });
            
        });
    });
    </script>
@endpush