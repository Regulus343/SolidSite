@if (!empty($buttons))

	<?php
	$iconElement     = config('html.icon.element', 'i');
	$iconClassPrefix = config('html.icon.class_prefix', 'fa fa-');
	?>

	<div{!! (isset($class) ? ' class="'.$class.'"' : '') . (isset($id) ? ' id="'.$id.'"' : '') !!}>

	@foreach ($buttons as $button)

		<{!! $button->tag.' '.Site::getButtonAttributes($button) !!}>

			@if (!is_null($button->icon))
				<{!! $iconElement !!} class="{!! $iconClassPrefix.$button->icon !!}"></{!! $iconElement !!}>
			@endif

			{!! entities($button->label) !!}

		</{!! $button->tag !!}>

	@endforeach

	</div>

@endif