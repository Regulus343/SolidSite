/*
|------------------------------------------------------------------------------
| SolidSite JS
|------------------------------------------------------------------------------
|
| Last Updated: May 3, 2016
|
*/

var SolidSite = {

	urls: {
		base: null,
		api:  null,
	},

	csrfToken:            null,
	suppressNextResponse: false,
	showMessageFunction:  null,
	pageLabel:            "Page",

	init: function()
	{
		this.initPagination();
	},

	setUrl: function(type, url)
	{
		this.urls[type] = url;
	},

	setUrls: function(urls)
	{
		for (type in urls)
		{
			this.setUrl(type, urls[type]);
		}
	},

	createUrl: function(uri, type)
	{
		if (typeof type == "undefined" || type == null)
			type = "base";

		return this.urls[type] + '/' + uri;
	},

	setCsrfToken: function(csrfToken)
	{
		this.csrfToken = csrfToken;

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': csrfToken,
			}
		});
	},

	call: function(config)
	{
		if (typeof config.maintainUrl == "undefined" || !config.maintainUrl)
			config.url = this.createUrl(config.url);

		if (typeof config.type == "undefined")
			config.type = "post";

		config.dataType = "json";

		config.xhrFields = {
			withCredentials: true,
		};

		if (typeof config.success == "undefined")
			config.success = function(response)
			{
				Api.processResponse(response, form);
			};

		$.ajax(config);
	},

	processResponse: function(response, form)
	{
		if (!this.suppressNextResponse)
		{
			if (typeof response == "string")
			{
				if (response.substr(0, 1) == "{")
				{
					response = $.parseJSON(response);
				}
				else
				{
					response = {
						type:    'Error',
						message: response,
						data:    {},
					};
				}
			}

			if (typeof response.message != undefined && response.message != false && this.showMessageFunction != null)
				this.executeFunction(this.showMessageFunction, response.message, response.type);

			if (typeof response.data.uri != "undefined")
				document.location.href = this.createUrl(response.data.uri);

			if (typeof response.data.url != "undefined")
				document.location.href = response.data.url;

			if (typeof form != "undefined")
			{
				if (response.type == "Success")
				{
					if (!form.data('prevent-modal-hide'))
						$(form).parents('.modal').find('[data-dismiss=modal]').trigger('click');

					// remove form errors
					form.find('.has-error').removeClass('has-error');
					form.find('.error').remove();

					// execute callback function
					var callbackFunction = form.data('callback-function');
					if (callbackFunction)
					{
						this.executeFunction(callbackFunction, response, form);
					}
				}
				else
				{
					if (typeof form != "undefined" && typeof response.data.errors == "object")
					{
						// set form errors
						form.find('.has-error').removeClass('has-error');
						form.find('.error').remove();

						for (fieldName in response.data.errors)
						{
							var error = response.data.errors[fieldName];
							var field = form.find('#field-'+fieldName.replace(/_/g, '-').replace(/\./g, '-'));

							if (!field.length)
								field = form.find('.field-'+fieldName.replace(/_/g, '-').replace(/\./g, '-'));

							field.parent('.form-group').addClass('has-error');
							field.addClass('has-error');

							var fieldNameLabel = fieldName.replace(/\_/g, ' ');
							if (field.data('field-name'))
							{
								fieldNameLabel = field.data('field-name');

								if (fieldNameLabel != fieldNameLabel.toUpperCase())
									fieldNameLabel = fieldNameLabel.charAt(0).toLowerCase() + fieldNameLabel.slice(1);
							}

							if (typeof error == "object")
								error = error[0];

							error = '<div class="error"><i class="fa fa-exclamation-triangle"></i> '+error.replace(fieldName.replace(/\_/g, ' '), fieldNameLabel)+'</div>';

							field.parents('.form-group').append(error);
						}

						// reset reCAPTCHA if one exists
						var reCaptcha = form.find('.g-recaptcha');
						if (reCaptcha.length)
							reCaptcha.reset();
					}
				}
			}
		}
		else
		{
			this.suppressNextResponse = false;
		}
	},

	executeFunction: function(functionItem, parameter1, parameter2)
	{
		if (functionItem === undefined)
			return null;

		if (typeof functionItem == "function")
			return functionItem(parameter1, parameter2);

		var functionArray = functionItem.split('.');

		if (functionArray.length == 3)
			return window[functionArray[0]][functionArray[1]][functionArray[2]](parameter1, parameter2);
		else if (functionArray.length == 2)
			return window[functionArray[0]][functionArray[1]](parameter1, parameter2);
		else
			return window[functionArray[0]](parameter1, parameter2);
	},

	initPagination: function()
	{
		$('.pagination li a').off('click').on('click', function(e)
		{
			e.preventDefault();

			var page = $(this).data('page');

			$('#field-page').val(page);

			$('.pagination li a').removeClass('btn-primary');
			$('.pagination li a[data-page="'+page+'"]').addClass('btn-primary');

			var paginationArea = $(this).parents('.pagination');

			var url = paginationArea.data('url');

			if (parseInt(page) > 1)
			{
				url += '/'+page;

				var pageTrailItem     = $('.breadcrumb li.page');
				var pageTrailItemText = SolidSite.pageLabel+' '+page;

				if (pageTrailItem.length)
				{
					pageTrailItem.find('a').attr('href', url);
					pageTrailItem.find('a').text(pageTrailItemText);
					pageTrailItem.show();
				}
				else
				{
					$('.breadcrumb li.active').removeClass('active');
					$('.breadcrumb').append('<li class="active page"><a href="'+url+'">'+pageTrailItemText+'</a>');
				}
			}
			else
			{
				$('.breadcrumb li.page').remove();
				$('.breadcrumb li:last-child').addClass('active');
			}

			history.pushState('', document.title, url);

			SolidSite.executeFunction(paginationArea.data('action'));
		});
	},

};