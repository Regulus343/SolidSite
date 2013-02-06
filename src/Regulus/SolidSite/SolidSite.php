<?php namespace Regulus\SolidSite;

/*----------------------------------------------------------------------------------------------------------
	SolidSite
		A composer package that assigns section name and titles to controller functions and simplifies
		creation of breadcrumb trails and is useful for other components that require page identifying
		information such as menus that highlight the current location.

		created by Cody Jassman
		last updated on January 31, 2013
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;
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
			if ($item != $itemToCompare) return '';
		}
		return static::selectedHTML($inClass);
	}

	/**
	 * Returns the "selected" class inside an existing class attribute, or return the "selected" class with a new class attribute.
	 *
	 * @param  array    $comparisonData
	 * @param  boolean  $inClass
	 * @return string
	 */
	private static function selectedHTML($inClass = false)
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
		if ($title != "") static::$trailItems[] = (object) array('title'=>$title, 'uri'=>$uri);
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