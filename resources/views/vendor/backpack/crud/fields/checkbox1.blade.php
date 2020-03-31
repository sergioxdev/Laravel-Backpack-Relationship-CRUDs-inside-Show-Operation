<!-- checkbox field -->

<div @include('crud::inc.field_wrapper_attributes') >
    @include('crud::inc.field_translatable_icon')    
    	<label>{!! $field['label'] !!}</label>
      <div class="checkbox">
    	  <input type="hidden" name="{{ $field['name'] }}" value="0">
    	  <input type="checkbox" value="1"

          name="{{ $field['name'] }}"

          @if (isset($field['value']))
            @if( ((int) $field['value'] == 1 || old($field['name']) == 1) && old($field['name']) !== '0' )
             checked="checked"
            @endif
          @elseif (isset($field['default']) && $field['default'])
            checked="checked"
          @endif

          @if (isset($field['attributes']))
              @foreach ($field['attributes'] as $attribute => $value)
    			{{ $attribute }}="{{ $value }}"
        	  @endforeach
          @endif
          style="position:relative !important; margin:0px 10px !important;"
          > 

        {{-- HINT --}}
        @if (isset($field['hint']))
            <p class="help-block">{!! $field['hint'] !!}</p>
        @endif
    </div>
</div>
