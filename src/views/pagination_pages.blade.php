<?php
$linkBookends     = Site::get('pagination.pageLinks.bookends');
$linkRadius       = Site::get('pagination.pageLinks.radius');
$bookendSeparator = Site::get('pagination.markup.bookendSeparator');

$start = $items->currentPage() - $linkRadius;
$end   = $items->currentPage() + $linkRadius;
?>

{{-- Previous Page --}}

@include('solid-site::pagination_page', ['p' => 'previous'])

{{-- Left Bookend --}}

@if ($items->currentPage() - $linkRadius > 1)

	<?php
	$bookendEnd = $linkBookends;
	if ($bookendEnd >= $start)
		$bookendEnd = $start - 1;
	?>

	@for ($p = 1; $p <= $bookendEnd; $p++)

		@include('solid-site::pagination_page', ['listItemClass' => 'bookend bookend-'.$p])

	@endfor

	@if ($bookendEnd < $start - 1)

		@include('solid-site::pagination_page', ['p' => 'separator', 'listItemClass' => 'separator'])

	@endif

@endif

{{-- Current Page + Radius --}}

@for ($p = $start; $p <= $end; $p++)

	@if ($p > 0 && $p <= $items->lastPage())

		@include('solid-site::pagination_page', ['listItemClass' => (abs($items->currentPage() - $p) ? 'radius radius-'.abs($items->currentPage() - $p) : 'current-page')])

	@endif

@endfor

{{-- Right Bookend --}}

@if ($items->currentPage() + $linkRadius < $items->lastPage())

	<?php
	$bookendStart = $items->lastPage() - $linkBookends + 1;
	if ($bookendStart <= $end)
		$bookendStart = $end;
	?>

	@if ($bookendStart > $end + 1)

		@include('solid-site::pagination_page', ['p' => 'separator', 'listItemClass' => 'separator'])

	@endif

	@for ($p = $bookendStart; $p <= $items->lastPage(); $p++)

		@include('solid-site::pagination_page', ['listItemClass' => 'bookend bookend-'.($items->lastPage() - $p + 1)])

	@endfor

@endif

{{-- Next Page --}}

@include('solid-site::pagination_page', ['p' => 'next'])