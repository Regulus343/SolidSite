{{-- SolidSite JS: Load --}}

<script type="text/javascript" src="{{ Site::js('solid-site', 'regulus/solid-site') }}"></script>

{{-- SolidSite JS: Initialize --}}

<script type="text/javascript">

	$(document).ready(function()
	{
		SolidSite.setUrl('{{ config('app.url') }}');

		SolidSite.setCsrfToken('{{ Session::token() }}');
	});

</script>