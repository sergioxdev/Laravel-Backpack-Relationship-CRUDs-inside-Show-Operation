{{-- regular object attribute --}}
@php
	$value = data_get($entry, $column['name']);

	if (is_array($value)) {
		$value = json_encode($value);
	}
	if(isset($column['replace']) && $column['replace'] == true && isset($column['str_replace']))
	{
		$value = str_replace($column['str_replace'],"",$value);
	}
@endphp

<span>
	{{ (array_key_exists('prefix', $column) ? $column['prefix'] : '').str_limit(strip_tags($value), array_key_exists('limit', $column) ? $column['limit'] : 50, "[...]").(array_key_exists('suffix', $column) ? $column['suffix'] : '') }}
</span>
