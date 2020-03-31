<!-- text input -->
<div @include('crud::inc.field_wrapper_attributes') >
    <label>{!! $field['label'] !!}</label>
    @include('crud::inc.field_translatable_icon')

	{{-- Show the file name and a "Clear" button on EDIT form. --}}
    @if (!empty($field['value']))
    <div class="well well-sm">
        @php
            $private = isset($field['private']) ? $field['private'] : 0;
            $link = isset($field['link']) ? $field['link'] : 0;
            $image = isset($field['image']) ? $field['image'] : 0;
            $t_image = $field['type']=="image" ? 1 : 0;
            $image = ((isset($field['image']) && $field['image']) || $t_image) ? 1 : 0;
        @endphp
        @if (isset($private) && $private)
            @php
                $disk = isset($field['disk']) ? $field['disk'] : "";
                $prefix = isset($field['prefix']) ? $field['prefix'] : "";
                $filename = isset($field['filename']) ? $field['filename'] : "";
                $file = isset($field['file']) ? $field['file'] : 0;
                $path = $field['value'];
            @endphp
            @if (isset($link) && $link)
                @php                
                    //$path =$crud->getCurrentFields()[$field_name]['value'];
                    $exists = Storage::disk($disk)->exists($path);
                    if($exists && $file && $link)
                    {
                        if($filename != "")
                            $filename_ = $filename;
                        else
                            $filename_ = str_replace($prefix."/", "", $path);

                        $filename = $path;
                    }
                    else
                    { 
                        $filename_ = "";
                    }

                    $route_name = Route::currentRouteName();
                    if(strpos($route_name, ".edit"))
                        $route_name = str_replace(".edit", ".downloadFile", $route_name);
                    if(strpos($route_name, ".show"))
                        $route_name = str_replace(".show", ".downloadFile", $route_name);
                    if(strpos($route_name, ".search"))
                        $route_name = str_replace(".search", ".downloadFile", $route_name);

                    $route_link = route($route_name);
                @endphp
                @if($private && $link && $file)
                    <a id="{{ $field['name'] }}_file_download" dat="{{$disk}}|{{$filename_}}|{{$filename}}" dlink="{{$route_link}}" href="#file" >{{$filename_}}</a>
                @else
                    {{$path}}
                @endif
            @elseif (isset($image) && $image)
                @php            
                $exists = Storage::disk($disk)->exists($path);
                if($exists && $image)
                {
                    $full_path = Storage::disk($disk)->path($path);
                    $base64 = base64_encode(Storage::disk($disk)->get($path));
                    $image_url = 'data:'.mime_content_type($full_path) . ';base64,' . $base64;
                }
                @endphp
                <img src="{!!$image_url!!}" style="height: 100px;">
            @else
                @if($filename != "")
                    {{$filename}}
                @else
                    {{$path}}
                @endif
            @endif
        @else
            @if (isset($field['link']) && $field['link'])
            @if (isset($field['disk']))
                @if (isset($field['temporary']))
                    <a target="_blank" href="{{ (asset(\Storage::disk($field['disk'])->temporaryUrl(array_get($field, 'prefix', '').$field['value'], Carbon\Carbon::now()->addMinutes($field['temporary'])))) }}">
                @else
                    <a target="_blank" href="{{ (asset(\Storage::disk($field['disk'])->url(array_get($field, 'prefix', '').$field['value']))) }}">
                @endif
            @else
                <a target="_blank" href="{{ (asset(array_get($field, 'prefix', '').$field['value'])) }}">
            @endif
            @endif
                {{ $field['value'] }}
            @if (isset($field['link']) && $field['link'])
            </a>
            @endif
        @endif
    	<a id="{{ $field['name'] }}_file_clear_button" href="#" class="btn btn-default btn-xs pull-right" title="Clear file"><i class="fa fa-remove"></i></a>
    	<div class="clearfix"></div>
    </div>
    @endif

	{{-- Show the file picker on CREATE form. --}}
	<input
        type="file"
        id="{{ $field['name'] }}_file_input"
        name="{{ $field['name'] }}"
        value="{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}"
        @include('crud::inc.field_attributes', ['default_class' =>  isset($field['value']) && $field['value']!=null?'form-control hidden':'form-control'])
    >

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
</div>

{{-- FIELD EXTRA JS --}}
{{-- push things in the after_scripts section --}}

    @push('crud_fields_scripts')
        <!-- no scripts -->
        <script>
	        $("#{{ $field['name'] }}_file_clear_button").click(function(e) {
	        	e.preventDefault();
	        	$(this).parent().addClass('hidden');

	        	var input = $("#{{ $field['name'] }}_file_input");
	        	input.removeClass('hidden');
	        	input.attr("value", "").replaceWith(input.clone(true));
	        	// add a hidden input with the same name, so that the setXAttribute method is triggered
	        	$("<input type='hidden' name='{{ $field['name'] }}' value=''>").insertAfter("#{{ $field['name'] }}_file_input");
	        });

	        $("#{{ $field['name'] }}_file_input").change(function() {
	        	// remove the hidden input, so that the setXAttribute method is no longer triggered
	        	$(this).next("input[type=hidden]").remove();
	        });
        </script>
        <script type="text/javascript">
        jQuery(document).ready(function($)
        {
            $("#{{ $field['name'] }}_file_download").click(function(e)
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
