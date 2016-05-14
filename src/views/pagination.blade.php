<input type="hidden" name="page" class="field-page"{!! (!Site::get('pagination.uiAdded') ? ' id="field-page"' : '') !!} value="{{ Site::get('pagination.currentPage', 1) }}" />

<ul class="pagination pull-right{{ ($items->lastPage() == 1 ? ' hidden' : '') }}" data-url="{{ Site::get('pagination.url') }}" data-action="{{ Site::get('pagination.function') }}">

	@include('solid-site::pagination_pages')

</ul>

<div class="clear"></div>