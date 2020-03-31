<!-- html5 color input -->
{{-- color attribute --}}
@php
	$value = $entry->{$column['name']};
	$style = 'style="height:90%; width: 90%; background: '.$value.';"';
@endphp
<div {!!$style!!}>&nbsp;</div>