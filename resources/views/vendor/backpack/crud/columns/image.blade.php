{{-- image column type --}}
@php
  $value = data_get($entry, $column['name']);

  if (is_array($value)) {
    $value = json_encode($value);
  }

    $prefix = isset($column['prefix']) ? $column['prefix'] : '';
    //$value = old(square_brackets_to_dots($column['name'])) ?? $column['value'] ?? $column['default'] ?? '';

    $private = isset($column['private']) ? $column['private'] : 0;
    $link = isset($column['link']) ? $column['link'] : 0;
    if($private)
    {
        $path = $value;
        $exists = Storage::disk($column['disk'])->exists($path);
        if($exists)
        {
            $full_path = Storage::disk($column['disk'])->path($path);
            $base64 = base64_encode(Storage::disk($column['disk'])->get($path));
            $image_url = 'data:'.mime_content_type($full_path) . ';base64,' . $base64;
        }
        else
        { 
            $value = "";
        }        
    }
    else
    { 
        $image_url = asset( (isset($column['prefix']) ? $column['prefix'] : '') . $value);
    }
@endphp

<span>
  @if( empty($value) )
    -
  @else
    @if($link)
    <a
      href="{{ asset( (isset($column['prefix']) ? $column['prefix'] : '') . $value) }}"
      target="_blank"
    >
    @endif
      <img
        src="{{ $image_url }}"
        style="
          max-height: {{ isset($column['height']) ? $column['height'] : "25px" }};
          width: {{ isset($column['width']) ? $column['width'] : "auto" }};
          border-radius: 3px;"
      />
    @if($link)
    </a>
    @endif
  @endif
</span>
