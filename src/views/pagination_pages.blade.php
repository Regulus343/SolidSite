<li{!! ($items->currentPage() == 1 ? ' class="disabled"' : '') !!}>
	<a href="" class="btn btn-sm btn-default" data-page="1">&laquo;</a>
</li>

@for ($p = $items->currentPage() - Site::get('pagination.pageLinkRadius'); $p <= $items->currentPage() + Site::get('pagination.pageLinkRadius'); $p++)

	@if ($p > 0 && $p <= $items->lastPage())

		<li>
			<a href="" class="{{ Site::get('pagination.classes.default').' '.($items->currentPage() == $p ? Site::get('pagination.classes.active') : Site::get('pagination.classes.inactive')) }}" data-page="{{ $p }}">
				{{ $p }}
			</a>
		</li>

	@endif

@endfor

<li{!! ($items->currentPage() == $items->lastPage() ? ' class="disabled"' : '') !!}>
	<a href="" class="btn btn-sm btn-default" data-page="{{ $items->lastPage() }}">&raquo;</a>
</li>