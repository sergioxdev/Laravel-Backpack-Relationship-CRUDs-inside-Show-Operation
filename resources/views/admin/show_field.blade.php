@if(isset($crud->getCurrentFields()[$field_name]) && $crud->getCurrentFields()[$field_name]['type']=="checkbox")
<span>
	@if($field==1)
    <i class="fa fa-check-square-o"></i>
    @else
    <i class="fa fa-square-o"></i>
    @endif
</span>
@elseif(isset($crud->getCurrentFields()[$field_name]) && ( $crud->getCurrentFields()[$field_name]['type']=="image" || $crud->getCurrentFields()[$field_name]['type']=="browse" || 
$crud->getCurrentFields()[$field_name]['type']=="upload") && $field != "")

	@php
		$private = isset($crud->getCurrentFields()[$field_name]['private']) ? $crud->getCurrentFields()[$field_name]['private'] : 0;
		$link = isset($crud->getCurrentFields()[$field_name]['link']) ? $crud->getCurrentFields()[$field_name]['link'] : 0;
        $t_image = $crud->getCurrentFields()[$field_name]['type']=="image" ? 1 : 0;
        $image = ((isset($crud->getCurrentFields()[$field_name]['image']) && $crud->getCurrentFields()[$field_name]['image']) || $t_image) ? 1 : 0;
	@endphp
	@if (isset($private) && $private)
		@php
			$disk = isset($crud->getCurrentFields()[$field_name]['disk']) ? $crud->getCurrentFields()[$field_name]['disk'] : "";
            $prefix = isset($crud->getCurrentFields()[$field_name]['prefix']) ? $crud->getCurrentFields()[$field_name]['prefix'] : "";
            $filename = isset($crud->getCurrentFields()[$field_name]['filename']) ? $crud->getCurrentFields()[$field_name]['filename'] : "";
            $file = isset($crud->getCurrentFields()[$field_name]['file']) ? $crud->getCurrentFields()[$field_name]['file'] : 0;
			$path = $crud->getCurrentFields()[$field_name]['value'];
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
                <a id="{{ $field_name }}_file_download" dat="{{$disk}}|{{$filename_}}|{{$filename}}" dlink="{{$route_link}}" href="#file" >{{$filename_}}</a>
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
    @endif

@elseif(isset($crud->getCurrentFields()[$field_name]) && $crud->getCurrentFields()[$field_name]['type']=="wysiwyg")
	<div style="word-wrap: anywhere;">{!!$field!!}</div>
@elseif(isset($crud->getCurrentFields()[$field_name]) && ($crud->getCurrentFields()[$field_name]['type']=="color" || $crud->getCurrentFields()[$field_name]['type']=="color_picker"))
	@php
		$style = 'style="height:90%; width: 50%; background: '.$field.';"';
	@endphp
	<div {!!$style!!}>&nbsp;</div>
@else
	{{$field}}
@endif