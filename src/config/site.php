<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Website Name and Email Settings
	|--------------------------------------------------------------------------
	|
	| The name and email addresses for the website. If an email address does
	| not contain the "@" character, "@website.com" will be appended to it when
	| using the email() method. The URL is based on the "url" config variable
	| in config/app.php.
	|
	*/
	'name'  => 'Website Name',
	'email' => [
		'enabled'   => true,
		'test_mode' => false,
		'addresses' => [
			'admin' => 'webmaster',
			'test'  => 'webmaster',
		],
	],

	/*
	|--------------------------------------------------------------------------
	| Assets, Image, SVG, CSS, JS, and Uploads Paths
	|--------------------------------------------------------------------------
	|
	| The path variables can be used to easily load assets using Site::asset(),
	| Site::img(), Site::css(), Site::js(), Site::svg(), and
	| Site::uploadedFile() to create URLS for your assets. "img",
	| "css", "js", and "uploads" will be appended to the end of
	| "assets", so by default Site::img('image-name') would create a URL
	| of http:://website.com/assets/images/image-name.png ("png" being the
	| assumed extension if one is not explicitly specified).
	|
	| The "public" variable is the relative path from your application
	| directory to the public directory. This is used in conjunction with the
	| Application class in case you wish to use a different directory structure
	| than Laravel 5's default.
	|
	*/
	'paths' => [
		'public'  => 'public',
		'root'    => '',
		'assets'  => 'assets',
		'images'  => 'images',
		'svg'     => 'svg',
		'css'     => 'css',
		'js'      => 'js',
		'uploads' => 'uploads',
	],

	/*
	|--------------------------------------------------------------------------
	| Section and Sub Section
	|--------------------------------------------------------------------------
	|
	| These are used to specify the section and sub section (if necessary) of
	| the website that the user is at. This can help to select active menu
	| elements, especially when used in conjunction with Site::selectBy().
	|
	*/
	'section'     => 'Home',
	'sub_section' => null,

	/*
	|--------------------------------------------------------------------------
	| Title
	|--------------------------------------------------------------------------
	|
	| "title.main" and "title.heading" are used to set and display titles.
	| "title" can be used with Site::title() to set a webpage's title within
	| HTML title tags. "heading" can be set if the heading on the page needs to
	| differ from the title of the web page. The Site::heading() method will
	| use "title" unless "heading" is set. "separator" can be used to separate
	| the website name from the page title. If "name_in_front" is true, the
	| website name will appear in front of the page title. By default, it is
	| false so Site::title() will place the page title in front of the
	| website name.
	|
	*/
	'title' => [
		'main'          => '',
		'heading'       => '',
		'separator'     => ' :: ',
		'name_in_front' => false,
	],

	/*
	|--------------------------------------------------------------------------
	| Selected Class
	|--------------------------------------------------------------------------
	|
	| The name of the "selected" class for Site::selectBy() and
	| Site::selectByMulti().
	|
	*/
	'selected_class' => 'selected',

	/*
	|--------------------------------------------------------------------------
	| Breadcrumb Trail
	|--------------------------------------------------------------------------
	|
	| The ID for breadcrumb trails.
	|
	*/
	'trail' => [
		'id' => 'breadcrumb-trail',
	],

	/*
	|--------------------------------------------------------------------------
	| Button List
	|--------------------------------------------------------------------------
	|
	| The default classes for button lists.
	|
	*/
	'buttons' => [
		'default_list_class'           => 'button-group',
		'default_class'                => 'btn btn-default',
		'default_class_always_present' => true,
	],

	/*
	|--------------------------------------------------------------------------
	| Pagination
	|--------------------------------------------------------------------------
	|
	| The pagination settings.
	|
	*/
	'pagination' => [
		'items_per_page'   => 25,
		'page_link_radius' => 3,
		'classes'          => [
			'default'  => 'btn btn-sm btn-default',
			'active'   => 'btn-primary',
			'inactive' => null,
		],
	],

];
