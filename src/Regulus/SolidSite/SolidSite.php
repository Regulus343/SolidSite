<?php namespace Regulus\SolidSite;

/*----------------------------------------------------------------------------------------------------------
	SolidSite
		A composer package that assigns section name and titles to controller functions and simplifies
		creation of breadcrumb trails and is useful for other components that require page identifying
		information such as menus that highlight the current location.

		created by Cody Jassman
		last updated on January 31, 2013
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\URL;

use Regulus\TetraText\TetraText as Format;

class SolidSite {

	public static $trailItems = array();

	/**
	 * Add an item to the breadcrumb trail.
	 *
	 * @param  string   $title
	 * @param  string   $uri
	 * @return void
	 */
	public static function addTrailItem($title = '', $uri = '') {
		if ($title != "") static::$trailItems[] = (object) array('title'=>$title, 'uri'=>$uri);
	}

	/**
	 * Add an item to the breadcrumb trail.
	 *
	 * @return array
	 */
	public static function getTrailItems() {
		return static::$trailItems;
	}

	/**
	 * Create HTML for breadcrumb trail.
	 *
	 * @param  string   $title
	 * @param  string   $uri
	 * @return void
	 */
	public static function createTrail($title = '', $uri = '') {
		$html = '';
		if (!empty(static::$trailItems)) {
			$html = '<ul id="breadcrumb-trail">';
			$first = true;
			foreach (static::$trailItems as $trailItem) {
				$html .= '<li>';
				if (!$first) $html .= '<span>'.Format::entities('&raquo;').'</span>';
				$html .= '<a href="'.URL::to($trailItem->uri).'">'.Format::entities($trailItem->title).'</a>';
				$html .= '</li>';
				$first = false;
			}
			$html .= '</ul>';
		}
		return $html;
	}

}