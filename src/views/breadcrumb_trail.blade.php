@if (!empty($trailItems) && !Site::get('hideBreadcrumbTrail'))

	<ol class="breadcrumb"{!! (!is_null($trailId) ? ' id="'.$trailId.'"' : '') !!}>

		@foreach ($trailItems as $i => $item)

			<li{!! Site::getTrailItemClass($item, $i) !!}>

				@if (!is_null($item->url))
					<a href="{{ $item->url }}">{!! entities($item->title) !!}</a>
				@else
					{!! entities($item->title) !!}
				@endif

			</li>

		@endforeach

	</ol>

@endif