<li{!! (isset($listItemClass) && !is_null($listItemClass) ? ' class="'.$listItemClass.'"' : '') !!}>

	<a href="{{ Site::getPageLink($p) }}" class="{{ Site::getPageLinkClass($p) }}" data-page="{{ Site::getPageNumber($p) }}">
		@if (in_array($p, ['previous', 'next', 'separator']))

			{{ Site::get('pagination.markup.'.$p) }}

		@elseif ($p == "separator")

			{{ Site::get('pagination.markup.separator') }}

		@else

			{{ $p }}

		@endif
	</a>

</li>