{{-- SolidSite JS: Load --}}

<script type="text/javascript" src="{{ Site::js('solid-site', 'regulus/solid-site') }}"></script>

{{-- SolidSite JS: Initialize --}}

<script type="text/javascript">

	$(document).ready(function()
	{
		var urls = {
			base: "{{ Site::rootUrl() }}",
			api:  "{{ Site::url(null, 'api') }}",
		};

		SolidSite.setUrls(urls);

		SolidSite.setCsrfToken('{{ csrf_token() }}');

		SolidSite.init();
	});

</script>