/*
|------------------------------------------------------------------------------
| SolidSite JS
|------------------------------------------------------------------------------
|
| Last Updated: March 11, 2015
|
*/

var SolidSite = {

	csrfToken: null,

	setCsrfToken: function(csrfToken)
	{
		return this.csrfToken = csrfToken;
	},

	getCsrfToken: function()
	{
		return this.csrfToken;
	},

	prepData: function (data)
	{
		data['_token'] = this.csrfToken;

		return data;
	},

}