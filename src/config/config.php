<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Website Name and Webmaster Email
	|--------------------------------------------------------------------------
	|
	| The name and webmaster email address for the website.
	|
	*/
	'name'  => 'Website Name',
	'email' => 'admin@localhost',

	/*
	|--------------------------------------------------------------------------
	| Assets, Image, SVG, CSS, JS, and Uploads Paths
	|--------------------------------------------------------------------------
	|
	| The URI variables can be used to easily load assets using Site::asset(),
	| Site::img(), Site::css(), Site::js(), Site::svg(), and
	| Site::uploadedFile() to create URLS for your assets. "imgURI", "cssURI",
	| "jsURI", and "uploadsURI" will be appended to the end of "assetsURI", so
	| by default Site::img('image-name') would create a URL of
	| http:://website.com/assets/img/image-name.png ("png" being the assumed
	| extension if one is not explicitly specified.
	|
	*/
	'assetsUri'  => 'assets',
	'imgUri'     => 'images',
	'svgUri'     => 'svg',
	'cssUri'     => 'css',
	'jsUri'      => 'js',
	'uploadsUri' => 'uploads',

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
	'section'    => 'Home',
	'subSection' => '',

	/*
	|--------------------------------------------------------------------------
	| Title
	|--------------------------------------------------------------------------
	|
	| "title" and "titleHeading" are used to set and display titles. "title"
	| can be used with Site::title() to set a webpage's title within HTML
	| title tags. "titleHeading" can be set if the heading on the page needs to
	| differ from the title of the web page. The Site::titleHeading() method
	| will use "title" unless "titleHeading" is set. "titleSeparator" can be
	| used to separate the website name from the page title. If
	| "titleNameInFront" is true, the website name will appear in front of the
	| page title. By default, it is false so Site::title() will place the page
	| title in front of the website name.
	|
	*/
	'title'            => '',
	'titleHeading'     => '',
	'titleSeparator'   => ' :: ',
	'titleNameInFront' => false,

	/*
	|--------------------------------------------------------------------------
	| Selected Class
	|--------------------------------------------------------------------------
	|
	| The name of the "selected" class for Site::selectBy() and
	| Site::selectByMulti().
	|
	*/
	'selectedClass' => 'selected',

	/*
	|--------------------------------------------------------------------------
	| Breadcrumb Trail
	|--------------------------------------------------------------------------
	|
	| The ID and the separator symbol for breadcrumb trails.
	|
	*/
	'trailId'        => 'breadcrumb-trail',
	'trailSeparator' => '»',

);