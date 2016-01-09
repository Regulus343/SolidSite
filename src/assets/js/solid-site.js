/*
|------------------------------------------------------------------------------
| SolidSite JS
|------------------------------------------------------------------------------
|
| Last Updated: January 8, 2016
|
*/

var SolidSite = {

	baseUrl:   null,
	csrfToken: null,

	setUrl: function(url, type)
	{
		if (type === undefined)
			type = "base";

		this[type+'Url'] = url;
	},

	createUrl: function(uri, type)
	{
		if (type === undefined)
			type = "base";

		return this[type+'Url'] + '/' + uri;
	},

	setCsrfToken: function(csrfToken)
	{
		$.ajaxSetup({
			headers: {'X-CSRF-TOKEN': csrfToken}
		});

		return this.csrfToken = csrfToken;
	},

	getCsrfToken: function()
	{
		return this.csrfToken;
	},

	prepData: function(data)
	{
		data['_token'] = this.csrfToken;

		return data;
	},

	prepConfig: function(config)
	{
		if (config.url === undefined && config.uri !== undefined)
			config.url = this.createUrl(uri);

		if (config.data !== undefined && config.data['_token'] === undefined)
			config.data = this.prepData(config.data);

		return config;
	},

	ajax: function(config)
	{
		return $.ajax(this.prepConfig(config));
	},

	get: function(url, data, success, dataType)
	{
		if (data !== undefined && data['_token'] === undefined)
			data = this.prepData(data);

		return $.get(url, data, success, dataType);
	},

	post: function(url, data, success, dataType)
	{
		if (data !== undefined && data['_token'] === undefined)
			data = this.prepData(data);

		return $.post(url, data, success, dataType);
	},

};