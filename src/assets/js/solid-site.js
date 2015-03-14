/*
|------------------------------------------------------------------------------
| SolidSite JS
|------------------------------------------------------------------------------
|
| Last Updated: March 13, 2015
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

	get: function(config)
	{
		return $.get(this.prepConfig(config));
	},

	post: function(config)
	{
		return $.post(this.prepConfig(config));
	},

}