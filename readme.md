SolidSite
=========

**A Laravel 5 composer package that assigns section names & titles to pages, simplifies creation of breadcrumb trails, pagination, and other components.**

> **Note:** For Laravel 4, you may use <a href="https://github.com/Regulus343/SolidSite/tree/v0.5.2">version 0.5.2</a>.

- [Installation](#installation)
- [Setting Sections, Sub Sections, and Titles](#setting-identifiers)
- [Highlighting Menu Items Based on Section and Sub Section](#highlighting-menu-items)
- [Asset URLs](#asset-urls)
- [Breadcrumb Trails](#breadcrumb-trails)
- [Button Lists](#button-lists)
- [Pagination](#pagination)
- [JavaScript](#js)

<a name="installation"></a>
## Installation

To install SolidSite, make sure "regulus/solid-site" has been added to Laravel 5's `composer.json` file.

	"require": {
		"regulus/solid-site": "0.7.*"
	},

Then run `php composer.phar update` from the command line. Composer will install the SolidSite package. Now, all you have to do is register the service provider and set up SolidSite's alias in `config/app.php`. Add this to the `providers` array:

	Regulus\SolidSite\SolidSiteServiceProvider::class,

And add this to the `aliases` array:

	'Site' => Regulus\SolidSite\Facade::class,

You may use 'SolidSite', or another alias, but 'Site' is recommended for the sake of simplicity. SolidSite is now ready to go.

Now, publish the config file, `site.php`, as well as the views and JS file, from the command line:

	php artisan vendor:publish

You may also use SolidSite's built-in extension of Laravel 5's `Illuminate\Foundation\Application` class in case you would like to modify the `public` directory to something else such as `public_html` or even `../public_html` if you place your Laravel 5 application inside another directory. SolidSite's own `Application` class allows you to set the `public_path` in `config/site.php` to restore this ability. If you would like to use it, replace this code in `bootstrap/app.php`:

	$app = new Illuminate\Foundation\Application(
		realpath(__DIR__.'/../')
	);

With this:

	$app = new Regulus\SolidSite\Application(
		realpath(__DIR__.'/../')
	);

This is, however, entirely optional. If you choose not to use it, you may still get SolidSite's configured public path using `Site::publicPath()`.

<a name="setting-identifiers"></a>
## Setting Sections, Sub Sections, and Titles

**Setting identifiers:**

	Site::set('section', 'Forum');

	Site::set(['subSection', 'title.main'], 'Forum: General'); // sets both to "Forum: General"

	Site::set([
		'section'    => 'Forum',
		'subSection' => 'General',
		'title.main' => 'Forum: General',
	]);

You can use the SolidSite package to store config items that you'd rather not store anywhere else. SolidSite has a few default identifiers including `section`, `subSection`, `title`, and `titleHeading`. These can be used to highlight menu items in a menu, or for anything else that requires a unique page identifier.

> **Note:** Though the variable names in the config file are snakecase, you may get and set them using camelcase as well.

**Getting identifiers:**

	Site::get('section');

**Setting the page title:**

	Site::setTitle('Title');

	Site::set('title.main', 'Title');

**Getting the page title:**

	Site::title();

This will return the `title.main` config item along with the `name` config item (the name of your website) or just the `name` config item if a title is not set.

**Setting the page heading:**

	Site::setHeading('Heading');

	Site::set('title.heading', 'Heading');

**Getting the page heading:**

	Site::heading();

This will return the `title.heading` config item or the `title` config item if a heading title is not set. This can be used in cases where you want to make the heading of the page different from what is in the web page's `title` tag. You may also use `setSubHeading()` and `subHeading()` should you require a sub heading.

<a name="highlighting-menu-items"></a>
## Highlighting Menu Items Based on Section and Sub Section

SolidSite has a few methods for the purpose of highlighting menu items (adding a "selected" class) when the correct section and/or sub section is set.

**Highlighting a menu item by section:**

	<ul>
		<li<?php echo Site::selectBy('section', 'Home'); ?>><a href="#">Home</a></li>
		<li<?php echo Site::selectBy('section', 'FAQ'); ?>><a href="#">FAQ</a></li>
		<li<?php echo Site::selectBy('section', 'Contact'); ?>><a href="#">Contact</a></li>
	</ul>

**Highlighting a menu item by multiple identifiers:**

	<ul>
		<li<?php echo Site::selectBy('section', 'Home'); ?>><a href="#">Home</a></li>
		<li<?php echo Site::selectBy('section', 'Stuff'); ?>>
			<a href="#">Stuff</a>
			<ul>
				<li<?php echo Site::selectByMulti(array('section' => 'Stuff', 'subSection' => 'Some Stuff')); ?>>
					<a href="#">Some Stuff</a>
				</li>
				<li class="some-class<?php echo Site::selectByMulti(array('section' => 'Stuff', 'subSection' => 'Some Other Stuff'), true); ?>">
					<a href="#">Some Other Stuff</a>
				</li>
			</ul>
		</li>
		<li<?php echo Site::selectBy('section', 'FAQ', false, 'active'); ?>><a href="#">FAQ</a></li>
		<li class="some-class<?php echo Site::selectBy('section', 'Contact', true); ?>"><a href="#">Contact</a></li>
	</ul>

If successful, a class will be added to the menu list item. The default class is "selected", but this can be adjusted in the config file or as the fourth argument in the `selectBy()` and `selectByMulti()` methods. The third class is a boolean that denotes whether the returned markup should be the entire class declaration or if it should just return the name of the class (with a preceding space) in case you want to add the class to an existing class declaration in your HTML.

<a name="asset-urls"></a>
## Asset URLs

**Create an asset URL that uses the directory specified in `config.php`:**

	echo Site::asset('js/jquery.js');

**Create an image asset URL:**

	echo Site::img('logo.png');

**Create a CSS asset URL:**

	echo Site::css('styles.css');

**Create a JavaScript asset URL:**

	echo Site::js('jquery.js');

**Create an uploaded file URL:**

	echo Site::uploadedFile('user_images/1.png');

The asset URLs methods should help to shorten markup and make your views cleaner. You can customize the directories for each of the asset types in `config.php` according to your preferences.

For `img()`, `css()`, and `js()`, you are not required to add an extension. For the image method, `.png` will be the assumed extension if you leave it out:

	echo Site::img('logo');   //automatically adds ".png"

	echo Site::css('styles'); //automatically adds ".css"

	echo Site::js('jquery');  //automatically adds ".js"

<a name="breadcrumb-trails"></a>
## Breadcrumb Trails

**Adding a breadcrumb trail item:**

	Site::addTrailItem('Home');
	Site::addTrailItem('Stuff', 'stuff');

The first argument is the title that will appear in the breadcrumb trail. The second argument is the URI route it links to. By default, it will link to the root page of your website. Setting the initial item in the `before()` filter in `filters.php` works well.

**Adding multiple breadcrumb trail items:**

	Site::addTrailItems([
		'Home'     => null, // will be base URL
		'Forum'    => 'forum', // will be like http://website.com/forum
		'Sections' => ['sections', 'forum'], // will be like http://forum.website.com/sections
	]);

**Outputting breadcrumb trail markup in a view:**

	{!! Site::getBreadcrumbTrailMarkup() !!}

> **Note:** If you would prefer to build the breadcrumb trail markup yourself, you can use `getTrailItems()`. The view for the breadcrumb trail will, however, be published to `resources/views/vendor/solid-site`, so you may also modify the existing one.

<a name="button-lists"></a>
## Button Lists

**Adding a button to the list:**

	Site::addButton('Home', 'home'); // simple version (label, URI / URL)

	Site::addButton([ // versatile version for more customization
		'uri'    => 'home',
		'label'  => 'Home',
		'class'  => 'btn btn-primary',
	]);

**Adding multiple buttons:**

	Site::addButtons([
		[
			'uri'    => 'home',
			'label'  => 'Home',
			'class'  => 'btn btn-primary',
		],
		[
			'url'    => [null, 'forum'], // will be like http://forum.website.com
			'label'  => 'Forum',
			'target' => '_blank',
		],
	]);

You may concurrently set multiple button lists as well. Each one has a sort of "namespace". You can change the active one with `setButtonList()` (the default is `main`), or you can simply add a `list` variable to the button item array. It is also possible to specify the list as the the second parameter of the `addButtons()` method.

**Adding multiple buttons:**

**Outputting button list markup in a view:**

	{!! Site::getButtonListMarkup() !!}

	{!! Site::getButtonListMarkup('secondary') !!}

The first example will use the active button list. The second one specifies the list to display.

<a name="pagination"></a>
## Pagination

SolidSite makes it easy to create URI segment-powered pagination rather than having to rely on GET query strings like `?page=2`. This can allow you to create pagination systems that feature very clean URLs.

**Setting up pagination:**

	$query = Items::where('type_id', 1); // set up a query using a model or the query builder

	$page = 2; // can be an integer, or simply "last" to get the last page

	Site::paginate($query, $page, [
		'uri'      => 'items', // you can pass a URI or URL for the pagination buttons
		'function' => 'Actions.getItems', // you can also pass a function to be called when a button is clicked
	]);

> **Note:** The `SolidSite` JavaScript class can be used in conjunction with `paginate()` to set up AJAX-based paginated data requesting when buttons are clicked.

<a name="js"></a>
## JavaScript

SolidSite includes a simple JavaScript class for defining app URLs (base URL and API URL are included as defaults), applying Laravel's CSRF token to all AJAX requests, executing function via strings, and initializing AJAX-based pagination. You may easily modify and add to the basic functions provided.

**Loading and initializing the JS:**

	@include('solid-site::load_js')

> **Note:** If you are using AJAX-based pagination, it's a good idea to return rendered markup of the pagination menu with each request so that you may update the page link radius (this radius will default to 3 links above and below the current page, assuming they exist).