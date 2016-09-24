<input type="hidden" name="page" class="field-page"{!! (!Site::get('pagination.uiAdded') ? ' id="field-page"' : '') !!} value="{{ Site::get('pagination.page', 1) }}" />

<ul class="pagination pull-right{{ ($items->lastPage() == 1 ? ' hidden' : '') }}" data-url="{{ Site::get('pagination.url') }}" data-url-suffix="{{ Site::get('pagination.urlSuffix') }}" data-action="{{ Site::get('pagination.function') }}">

	@include('solid-site::pagination_pages')

</ul>

<div class="clear"></div>