SolidSite
=========

A composer package that assigns section name and titles to controller functions and simplifies creation of breadcrumb trails and is useful for other components that require page identifying information such as menus that highlight the current location.

- [Installation](#installation)
- [Setting Sections, Sub Sections, and Titles](#setting-identifiers)
- [Highlighting Menu Items Based on Section and Sub Section](#highlighting-menu-items)
- [Creating a Breadcrumb Trail](#creating-breadcrumb-trail)

<a name="installation"></a>
## Installation

To install SolidSite, make sure "regulus/solid-site" has been added to Laravel 4's config.json file.

	"require": {
		"regulus/solid-site": "dev-master"
	},

Then run `php composer.phar update` from the command line. Composer will install the SolidSite package. Now, all you have to do is register the service provider and set up SolidSite's alias in `app/config/app.php`. Add this to the `providers` array:

	'Aquanode\SolidSite\SolidSiteServiceProvider',

And add this to the `aliases` array:

	'Site' => 'Aquanode\SolidSite\SolidSite',

You may use 'SolidSite', or another alias, but 'Site' is recommended for the sake of simplicity. SolidSite is now ready to go.

<a name="setting-identifiers"></a>
## Setting Sections, Sub Sections, and Titles

**Setting any identifier:**

	Site::set('section', 'Forum');
	Site::set('subSection', 'Forum: General');
	Site::set('title', 'Forum: 'Forum: General');

You can use the SolidSite package to store config items that you'd rather not store anywhere else. SolidSite has a few default identifiers including `section`, `subSection`, `title`, and `titleHeading`. These can be used to highlight menu items in a menu, or for anything else that requires a unique page identifier.

**Getting any identifier:**

	Site::get('section')

**Getting the page title:**

	Site::title();

This will return the `title` config item or the `name` config item (the name of your website) if a title is not set.

**Getting the heading title:**

	Site::titleHeading

This will return the `titleHeading` config item or the `title` config item if a heading title is not set. This can be used in cases where you want to make the heading of the page different from what is in the web page's `title` tag.

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
				<li<?php echo Site::selectByMult(array('section' => 'Stuff', 'subSection' => 'Some Stuff')); ?>>
					<a href="#">Some Stuff</a>
				</li>
				<li class="some-class<?php echo Site::selectByMult(array('section' => 'Stuff', 'subSection' => 'Some Other Stuff'), true); ?>">
					<a href="#">Some Other Stuff</a>
				</li>
			</ul>
		</li>
		<li<?php echo Site::selectBy('section', 'FAQ'); ?>><a href="#">FAQ</a></li>
		<li<?php echo Site::selectBy('section', 'Contact'); ?>><a href="#">Contact</a></li>
	</ul>

If successful, a class will be added to the menu list item. The default class is "selected", but this can be adjusted in the config file.

<a name="creating-breadcrumb-trail"></a>
## Creating a Breadcrumb Trail

**Adding a breadcrumb trail item:**

	Site::addTrailItem('Home');
	Site::addTrailItem('Stuff', 'stuff')

The first argument is the title that will appear in the breadcrumb trail. The second argument is the URI route it links to. By default, it will link to the root page of your website. Setting the initial item in the `before()` filter in `filters.php` works well.

**Creating a breadcrumb trail in a view:**

	echo Site::createTrail();

> **Note:** If you would prefer to build the breadcrumb trail markup yourself, you can use `getTrailItems()`.