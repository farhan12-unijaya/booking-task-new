<?php
$name = ($name or false) ? $name : null;
?>
<div class="form-group row">
	<label {{ "id=label_$name" }} for="{{ $name or '' }}" class="col-md-3 control-label">
		{{ $label or 'Input Field' }}
		@isset($mode) @if(strpos($mode, 'required') !== false)<span style="color:red;">*</span>@endif @endisset
		@isset($info) @include('components.info', ['text' => $info]) @endisset
	</label>
	<div class="col-md-9">
		<input 
		id="{{ $name or '' }}"
		class="form-control {{ $class or '' }}" 
		name="{{ $name or '' }}" 
		{{ $mode or '' }}
		type="{{ $type or 'text' }}"
		value="{{ $value or '' }}"
		{{ $options or '' }}
		>
	</div>
</div>