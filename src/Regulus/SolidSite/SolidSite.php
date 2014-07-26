<?php namespace Regulus\SolidSite;

/*----------------------------------------------------------------------------------------------------------
	SolidSite
		A composer package that assigns section name and titles to controller functions and simplifies
		creation of breadcrumb trails and is useful for other components that require page identifying
		information such as menus that highlight the current location.

		created by Cody Jassman
		v0.4.0
		last updated on July 26, 2014
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use Regulus\TetraText\TetraText as Format;

class SolidSite {

	/**
	 * @var    array
	 */
	public $trailItems = array();

	/**
	 * Get a config item.
	 *
	 * @param  string   $item
	 * @return string
	 */
	public function get($item)
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
	public function set($item, $value = null)
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
	public function setMulti($items = array(), $value = null)
	{
		foreach ($items as $item) {
			$this->set($item, $value);
		}
	}

	/**
	 * Get the website name.
	 *
	 * @return string
	 */
	public function name()
	{
		return $this->get('name');
	}

	/**
	 * Get the title for a web page's title tag.
	 *
	 * @param  string   $title
	 * @return string
	 */
	public function title($title = null)
	{
		if (is_null($title)) $title = $this->get('title');
		$title = strip_tags($title);
		if (is_null($title) || $title == "") {
			return $this->get('name');
		} else {
			//var_dump(Config::get('solid-site::titleSeparator')); exit;
			if ($this->get('titleNameInFront')) {
				return $this->get('name').$this->get('titleSeparator').$title;
			} else {
				return $title.$this->get('titleSeparator').$this->get('name');
			}
		}
	}

	/**
	 * Get the heading title. If no specific heading title is set, the regular title is used.
	 *
	 * @return string
	 */
	public function titleHeading()
	{
		$title = $this->get('titleHeading');
		if (is_null($title) || $title == "" || !is_string($title))
			$title = $this->get('title');

		if (!is_string($title))
			$title = "";

		if (strip_tags($title) == $title)
			$title = Format::entities($title);

		return $title;
	}

	/**
	 * Create a URL from websites root URL defined in config. This can be useful when using subdomains
	 * because URL::to() will create a URL with the current subdomain instead of the website's root.
	 *
	 * @param  string   $uri
	 * @param  string   $secure
	 * @return string
	 */
	public function rootUrl($uri = '', $secure = false)
	{
		$url = Config::get('app.url');
		if ($secure)
			$url = str_replace('http://', 'https://', $url);

		if ($uri != "")
			$url .= '/'.$uri;

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
	public function asset($path = '', $secure = false, $package = false)
	{
		if ($package)
			$path = 'packages/'.$package.'/'.$path;
		else
			$path = $this->get('assetsUri').'/'.$path;

		return $this->rootURL($path, $secure);
	}

	/**
	 * Create a URL for an image.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  string   $addExtension
	 * @return string
	 */
	public function img($path = '', $package = false, $addExtension = true)
	{
		//if no extension is given, assume .png
		if ($addExtension && $path != "" && !in_array(File::extension($path), array('png', 'jpg', 'jpeg', 'jpe', 'gif', 'svg'))) {
			$path .= ".png";
		}

		$path = $this->get('imgUri').'/'.$path;

		if ($package)
			$path = 'packages/'.$package.'/'.$path;
		else
			$path = $this->get('assetsUri').'/'.$path;

		return $this->rootURL($path);
	}

	/**
	 * Create a URL for a CSS file.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  string   $addExtension
	 * @return string
	 */
	public function css($path = '', $package = false, $addExtension = true)
	{
		//add .css extension if one doesn't exist
		if ($path != "" && File::extension($path) != "css")
			$path .= ".css";

		$path = $this->get('cssUri').'/'.$path;

		if ($package)
			$path = 'packages/'.$package.'/'.$path;
		else
			$path = $this->get('assetsUri').'/'.$path;

		return $this->rootURL($path);
	}

	/**
	 * Create a URL for a JavaScript file.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  string   $addExtension
	 * @return string
	 */
	public function js($path = '', $package = false, $addExtension = true)
	{
		//add .js extension if one doesn't exist
		if ($path != "" && File::extension($path) != "js")
			$path .= ".js";

		$path = $this->get('jsUri').'/'.$path;

		if ($package)
			$path = 'packages/'.$package.'/'.$path;
		else
			$path = $this->get('assetsUri').'/'.$path;

		return $this->rootURL($path);
	}

	/**
	 * Create a URL for an uploaded file.
	 *
	 * @param  string   $path
	 * @return string
	 */
	public function uploadedFile($path = '')
	{
		return $this->rootUrl($this->get('uploadsUri').'/'.$path);
	}

	/**
	 * Adds the "selected" class to an HTML element if the first variable matches the second variable.
	 *
	 * @param  string   $item
	 * @param  string   $itemToCompare
	 * @param  boolean  $inClass
	 * @param  mixed    $className
	 * @return string
	 */
	public function selectForMatch($item, $itemToCompare, $inClass = false, $className = false)
	{
		$matches = false;
		if (is_array($itemToCompare)) {
			if (in_array($item, $itemToCompare))
				$matches = true;
		} else {
			if ($item == $itemToCompare)
				$matches = true;
		}
		if ($matches) return $this->selectedHtml($inClass, $className);
		return '';
	}

	/**
	 * Adds the "selected" class to an HTML element if the declared config item matches the variable.
	 *
	 * @param  string   $item
	 * @param  string   $itemToCompare
	 * @param  boolean  $inClass
	 * @param  mixed    $className
	 * @return string
	 */
	public function selectBy($type = 'section', $itemToCompare = '', $inClass = false, $className = false)
	{
		return $this->selectForMatch($this->get($type), $itemToCompare, $inClass, $className);
	}

	/**
	 * Adds the "selected" class to an HTML element if all declared config items match their respective comparison variables (associative array).
	 *
	 * @param  array    $comparisonData
	 * @param  boolean  $inClass
	 * @param  mixed    $className
	 * @return string
	 */
	public function selectByMulti($comparisonData = array(), $inClass = false, $className = false)
	{
		foreach ($comparisonData as $item => $itemToCompare) {
			if ($this->get($item) != $itemToCompare) return '';
		}
		return $this->selectedHtml($inClass, $className);
	}

	/**
	 * Returns the "selected" class inside an existing class attribute, or return the "selected" class with a new class attribute.
	 *
	 * @param  array    $comparisonData
	 * @param  boolean  $inClass
	 * @param  mixed    $className
	 * @return string
	 */
	private function selectedHtml($inClass = false, $className = false)
	{
		if (!$className || !is_string($className) || $className == "") $className = $this->get('selectedClass');

		if ($inClass) {
			return ' '.$className;
		} else {
			return ' class="'.$className.'"';
		}
	}

	/**
	 * Add an item to the breadcrumb trail.
	 *
	 * @param  string   $title
	 * @param  string   $uri
	 * @return void
	 */
	public function addTrailItem($title = '', $uri = '')
	{
		if ($title != "") $this->trailItems[] = (object) array('title' => $title, 'uri' => $uri);
	}

	/**
	 * Add an item to the breadcrumb trail.
	 *
	 * @return array
	 */
	public function getTrailItems()
	{
		return $this->trailItems;
	}

	/**
	 * Create HTML for breadcrumb trail.
	 *
	 * @param  string   $title
	 * @param  string   $uri
	 * @return string
	 */
	public function createTrail($id = null)
	{
		$html = '';
		if (is_null($id))
			$id = $this->get('trailId');

		if (!empty($this->trailItems)) {
			$html  = '<ul id="'.$id.'">';
			$first = true;

			foreach ($this->trailItems as $trailItem) {
				$html .= '<li>';

				if (!$first)
					$html .= '<span>'.Format::entities($this->get('trailSeparator')).'</span>';

				$html .= '<a href="'.URL::to($trailItem->uri).'">'.Format::entities($trailItem->title).'</a>';
				$html .= '</li>';
				$first = false;
			}
			$html .= '</ul>';
		}

		return $html;
	}

	/**
	 * Set the user to "developer" status.
	 *
	 * @return void
	 */
	public function setDeveloper()
	{
		Session::set('developer', true);
	}

	/**
	 * Get the user's "developer" status based on session variable.
	 *
	 * @return boolean
	 */
	public function developer()
	{
		$developer = Session::get('developer');
		return !is_null($developer) && $developer ? true : false;
	}

}