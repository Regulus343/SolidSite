<?php namespace Regulus\SolidSite;

/*----------------------------------------------------------------------------------------------------------
	SolidSite
		A composer package that assigns section name and titles to controller functions and simplifies
		creation of breadcrumb trails and is useful for other components that require page identifying
		information such as menus that highlight the current location.

		created by Cody Jassman
		last updated on March 20, 2013
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;

use Regulus\TetraText\TetraText as Format;

class SolidSite {

	public static $trailItems = array();

	/**
	 * Get a config item.
	 *
	 * @param  string   $item
	 * @return string
	 */
	public static function get($item)
	{
		return Config::get('solid-site::'.$item);
	}

	/**
	 * Set a config item.
	 *
	 * @param  string   $item
	 * @param  mixed    $value
	 * @return void
	 */
	public static function set($item, $value = null)
	{
		Config::set('solid-site::'.$item, $value);
	}

	/**
	 * Set multiple config items to one particular value.
	 *
	 * @param  array    $items
	 * @param  mixed    $value
	 * @return void
	 */
	public static function setMulti($items = array(), $value = null)
	{
		foreach ($items as $item) {
			static::set($item, $value);
		}
	}

	/**
	 * Get the website name.
	 *
	 * @return string
	 */
	public static function name()
	{
		return static::get('name');
	}

	/**
	 * Get the title for a web page's title tag.
	 *
	 * @param  string   $title
	 * @return string
	 */
	public static function title($title = null)
	{
		if (is_null($title)) $title = static::get('title');
		if (is_null($title) || $title == "") {
			return static::get('name');
		} else {
			if (static::get('titleNameInFront')) {
				return static::get('name').static::get('titleSeparator').$title;
			} else {
				return $title.static::get('titleSeparator').static::get('name');
			}
		}
	}

	/**
	 * Get the heading title. If no specific heading title is set, the regular title is used.
	 *
	 * @return string
	 */
	public static function titleHeading()
	{
		$title = static::get('titleHeading');
		if (is_null($title) || $title == "") {
			return static::get('title');
		} else {
			return $title;
		}
	}

	/**
	 * Create a URL from websites root URL defined in config. This can be useful when using subdomains
	 * because URL::to() will create a URL with the current subdomain instead of the website's root.
	 *
	 * @param  string   $uri
	 * @param  string   $secure
	 * @return string
	 */
	public static function rootUrl($uri = '', $secure = false) {
		$url = static::get('url');
		if ($secure) $url = str_replace('http://', 'https://', $url);
		if ($uri != "") $url .= '/'.$uri;
		return $url;
	}

	/**
	 * Create a URL for an asset.
	 *
	 * @param  string   $path
	 * @param  boolean  $secure
	 * @param  mixed    $package
	 * @return string
	 */
	public static function asset($path = '', $secure = false, $package = false) {
		$path = static::get('assetsURI').'/'.$path;
		if ($package) $path = 'packages/'.$package.'/'.$path;

		return static::rootUrl($path, $secure);
	}

	/**
	 * Create a URL for an image.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  string   $addExtension
	 * @return string
	 */
	public static function img($path = '', $package = false, $addExtension = true) {
		//if no extension is given, assume .png
		if ($addExtension && $path != "" && !in_array(File::extension($path), array('png', 'jpg', 'jpeg', 'jpe', 'gif'))) {
			$path .= ".png";
		}
		$path = static::get('assetsURI').'/'.static::get('imgURI').'/'.$path;
		if ($package) $path = 'packages/'.$package.'/'.$path;

		return static::rootUrl($path);
	}

	/**
	 * Create a URL for a CSS file.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  string   $addExtension
	 * @return string
	 */
	public static function css($path = '', $package = false, $addExtension = true) {
		//add .css extension if one doesn't exist
		if ($path != "" && File::extension($path) != "css") $path .= ".css";

		$path = static::get('assetsURI').'/'.static::get('cssURI').'/'.$path;
		if ($package) $path = 'packages/'.$package.'/'.$path;

		return static::rootUrl($path);
	}

	/**
	 * Create a URL for a JavaScript file.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  string   $addExtension
	 * @return string
	 */
	public static function js($path = '', $package = false, $addExtension = true) {
		//add .js extension if one doesn't exist
		if ($path != "" && File::extension($path) != "js") $path .= ".js";

		$path = static::get('assetsURI').'/'.static::get('jsURI').'/'.$path;
		if ($package) $path = 'packages/'.$package.'/'.$path;

		return static::rootUrl($path);
	}

	/**
	 * Create a URL for an uploaded file.
	 *
	 * @param  string   $path
	 * @return string
	 */
	public static function uploadedFile($path = '') {
		return static::rootUrl(static::get('uploadsURI').'/'.$path);
	}

	/**
	 * Adds the "selected" class to an HTML element if the first variable matches the second variable.
	 *
	 * @param  string   $item
	 * @param  string   $itemToCompare
	 * @param  boolean  $inClass
	 * @return string
	 */
	public static function selectForMatch($item, $itemToCompare, $inClass = false)
	{
		if ($item == $itemToCompare) return static::selectedHTML($inClass);
		return '';
	}

	/**
	 * Adds the "selected" class to an HTML element if the declared config item matches the variable.
	 *
	 * @param  string   $item
	 * @param  string   $itemToCompare
	 * @param  boolean  $inClass
	 * @return string
	 */
	public static function selectBy($type = 'section', $itemToCompare = '', $inClass = false)
	{
		return static::selectForMatch(static::get($type), $itemToCompare, $inClass);
	}

	/**
	 * Adds the "selected" class to an HTML element if all declared config items match their respective comparison variables (associative array).
	 *
	 * @param  array    $comparisonData
	 * @param  boolean  $inClass
	 * @return string
	 */
	public static function selectByMulti($comparisonData = array(), $inClass = false)
	{
		foreach ($comparisonData as $item => $itemToCompare) {
			if (static::get($item) != $itemToCompare) return '';
		}
		return static::selectedHtml($inClass);
	}

	/**
	 * Returns the "selected" class inside an existing class attribute, or return the "selected" class with a new class attribute.
	 *
	 * @param  array    $comparisonData
	 * @param  boolean  $inClass
	 * @return string
	 */
	private static function selectedHtml($inClass = false)
	{
		if ($inClass) {
			return ' '.static::get('selectedClass');
		} else {
			return ' class="'.static::get('selectedClass').'"';
		}
	}

	/**
	 * Add an item to the breadcrumb trail.
	 *
	 * @param  string   $title
	 * @param  string   $uri
	 * @return void
	 */
	public static function addTrailItem($title = '', $uri = '')
	{
		if ($title != "") static::$trailItems[] = (object) array('title' => $title, 'uri' => $uri);
	}

	/**
	 * Add an item to the breadcrumb trail.
	 *
	 * @return array
	 */
	public static function getTrailItems()
	{
		return static::$trailItems;
	}

	/**
	 * Create HTML for breadcrumb trail.
	 *
	 * @param  string   $title
	 * @param  string   $uri
	 * @return void
	 */
	public static function createTrail($id = null)
	{
		$html = '';
		if (is_null($id)) $id = static::get('trailID');
		if (!empty(static::$trailItems)) {
			$html = '<ul id="'.$id.'">';
			$first = true;
			foreach (static::$trailItems as $trailItem) {
				$html .= '<li>';
				if (!$first) $html .= '<span>'.Format::entities(static::get('trailSeparator')).'</span>';
				$html .= '<a href="'.URL::to($trailItem->uri).'">'.Format::entities($trailItem->title).'</a>';
				$html .= '</li>';
				$first = false;
			}
			$html .= '</ul>';
		}
		return $html;
	}

}