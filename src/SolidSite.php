<?php namespace Regulus\SolidSite;

/*----------------------------------------------------------------------------------------------------------
	SolidSite
		A Laravel 5 composer package that assigns section names & titles to pages, simplifies creation of
		breadcrumb trails, and is useful for other components that require page identifying information
		such as menus that highlight the current location.

		created by Cody Jassman
		v0.6.0
		last updated on March 3, 2015
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

use Regulus\TetraText\Facade as Format;

class SolidSite {

	/**
	 * @var    array
	 */
	public $trailItems = [];

	/**
	 * @var    array
	 */
	public $buttons = [];

	/**
	 * Get a config item.
	 *
	 * @param  string   $item
	 * @param  mixed    $default
	 * @return mixed
	 */
	public function get($item, $default = null)
	{
		return Config::get('site.'.snake_case($item), $default);
	}

	/**
	 * Set a config item.
	 *
	 * @param  string   $item
	 * @param  mixed    $value
	 * @return mixed
	 */
	public function set($item, $value = null)
	{
		Config::set('site.'.snake_case($item), $value);

		return $value;
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
	 * Get the website email.
	 *
	 * @return string
	 */
	public function email()
	{
		$email = $this->get('email');

		if (strpos($email, '@') === false)
			$email .= '@'.config('app.url');

		return $email;
	}

	/**
	 * Get the title for a web page's title tag.
	 *
	 * @param  mixed    $title
	 * @return string
	 */
	public function title($title = null)
	{
		if (is_null($title))
			$title = $this->get('title.main');

		$title = strip_tags($title);
		if (is_null($title) || $title == "") {
			return $this->get('name');
		} else {
			if ($this->get('title.nameInFront'))
				return $this->get('name').$this->get('title.separator').$title;
			else
				return $title.$this->get('title.separator').$this->get('name');
		}
	}

	/**
	 * Get the heading title. If no specific heading title is set, the regular title is used.
	 *
	 * @param  boolean  $useSiteName
	 * @return string
	 */
	public function heading($useSiteName = false)
	{
		$title = $this->get('title.heading');

		if (is_null($title) || $title == "" || !is_string($title))
			$title = $this->get('title.main');

		if ((is_null($title) || $title == "" || !is_string($title)) && $useSiteName)
			$title = $this->name();

		if (!is_string($title))
			$title = "";

		if (strip_tags($title) == $title)
			$title = Format::entities($title);

		return $title;
	}

	/**
	 * Set the page title.
	 *
	 * @param  string   $item
	 * @param  mixed    $value
	 * @return mixed
	 */
	public function setTitle($value = null)
	{
		$this->set('title.main', $value);

		return $value;
	}

	/**
	 * Set the page heading.
	 *
	 * @param  string   $item
	 * @param  mixed    $value
	 * @return mixed
	 */
	public function setHeading($value = null)
	{
		$this->set('title.heading', $value);

		return $value;
	}

	/**
	 * Create a URL.
	 *
	 * @param  mixed    $uri
	 * @param  mixed    $subdomain
	 * @param  boolean  $secure
	 * @return string
	 */
	public function url($uri = null, $subdomain = null, $secure = false)
	{
		if (is_null($uri) || $uri === false)
			$uri = "";

		$url = URL::to($uri);

		$baseUrl = str_replace('https://', '', str_replace('http://', '', Config::get('app.url')));

		if ($subdomain !== true)
		{
			//remove subdomain if one exists
			$url = preg_replace('/(http[s]?:\/\/)[A-Za-z0-9]*[\.]?('.str_replace('.', '\.', $baseUrl).')/', '${1}${2}', $url);

			//add subdomain if one is set
			if ($subdomain != "" && $subdomain !== false && !is_null($subdomain))
				$url = preg_replace('/(http[s]?:\/\/)('.str_replace('.', '\.', $baseUrl).')/', '${1}'.$subdomain.'.${2}', $url);
		}

		if ($secure)
			$url = str_replace('http://', 'https://', $url);

		return $url;
	}

	/**
	 * Create a URL from websites root URL defined in config. This can be useful when using subdomains
	 * because URL::to() will create a URL with the current subdomain instead of the website's root.
	 *
	 * @param  string   $uri
	 * @param  boolean  $secure
	 * @return string
	 */
	public function rootUrl($uri = '', $secure = false)
	{
		return $this->url($uri, null, $secure);
	}

	/**
	 * Get the path to the public / web directory.
	 *
	 * @return string
	 */
	public function publicPath()
	{
		return base_path().DIRECTORY_SEPARATOR.config('site.public_path');
	}

	/**
	 * Get the assets directory path.
	 *
	 * @param  string   $path
	 * @param  boolean  $fromAppDir
	 * @return string
	 */
	public function assetsPath($relativePath = '', $fromAppDir = false)
	{
		$path = ($fromAppDir ? public_path() : $this->get('rootPath'));

		if (is_null($path) || $path == "" || !$path)
			$path = $this->get('assetsPath');
		else
			$path .= '/'.$this->get('assetsPath');

		if (!is_null($relativePath) && $relativePath != "" && $relativePath)
			$path .= '/'.$relativePath;

		return str_replace('//', '/', $path);
	}

	/**
	 * Get the directory for a path.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @return string
	 */
	public function getDirectoryForPath($path = '', $package = false)
	{
		if ($package)
			$path = $this->assetsPath().'/'.$package.'/'.$path;
		else
			$path = $this->assetsPath().'/'.$path;

		return $path;
	}

	/**
	 * Create a URL for an asset.
	 *
	 * @param  string   $path
	 * @param  boolean  $secure
	 * @param  mixed    $package
	 * @param  mixed    $useRoot
	 * @return string
	 */
	public function asset($path = '', $secure = false, $package = false, $useRoot = null)
	{
		$path = $this->getDirectoryForPath($path, $package);

		if (is_null($useRoot))
			$useRoot = $this->get('useRoot');

		return $useRoot ? $this->rootUrl($path) : $this->url($path, true);
	}

	/**
	 * Create a URL for an image.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  string   $addExtension
	 * @param  mixed    $useRoot
	 * @return string
	 */
	public function img($path = '', $package = false, $addExtension = true, $useRoot = null)
	{
		//if no extension is given, assume .png
		if ($addExtension && $path != "" && !in_array(File::extension($path), ['png', 'jpg', 'jpeg', 'jpe', 'gif', 'ico', 'svg']))
			$path .= ".png";

		$path = $this->getDirectoryForPath($this->get('imgPath').'/'.$path, $package);

		if (is_null($useRoot))
			$useRoot = $this->get('useRoot');

		return $useRoot ? $this->rootUrl($path) : $this->url($path, true);
	}

	/**
	 * Alias for img() method.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  string   $addExtension
	 * @param  mixed    $useRoot
	 * @return string
	 */
	public function image($path = '', $package = false, $addExtension = true, $useRoot = null)
	{
		return $this->img($path, $package, $addExtension, $useRoot);
	}

	/**
	 * Create a URL for a CSS file.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  mixed    $useRoot
	 * @return string
	 */
	public function css($path = '', $package = false, $useRoot = null)
	{
		//add .css extension if one doesn't exist
		if ($path != "" && File::extension($path) != "css")
			$path .= ".css";

		$path = $this->getDirectoryForPath($this->get('cssPath').'/'.$path, $package);

		if (is_null($useRoot))
			$useRoot = $this->get('useRoot');

		return $useRoot ? $this->rootUrl($path) : $this->url($path, true);
	}

	/**
	 * Create a URL for a JavaScript file.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  string   $addExtension
	 * @param  mixed    $useSubdomain
	 * @return string
	 */
	public function js($path = '', $package = false, $addExtension = true, $useRoot = null)
	{
		//add .js extension if one doesn't exist
		if ($path != "" && File::extension($path) != "js")
			$path .= ".js";

		$path = $this->getDirectoryForPath($this->get('jsPath').'/'.$path, $package);

		if (is_null($useRoot))
			$useRoot = $this->get('useRoot');

		return $useRoot ? $this->rootUrl($path) : $this->url($path, true);
	}

	/**
	 * Get the contents of an SVG file.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @return string
	 */
	public function svg($path = '', $package = false)
	{
		//if no extension is given, add .svg
		if ($path != "" && File::extension($path) != "svg")
			$path .= ".svg";

		$svgPath = $this->get('svgPath');
		if ($svgPath != "" && $svgPath != false && !is_null($svgPath))
			$path = $svgPath.'/'.$path;

		$path = $this->getDirectoryForPath($this->get('imgPath').'/'.$path, $package);

		if (is_file($path))
			return file_get_contents($path);
		else
			return "";
	}

	/**
	 * Create a URL for an uploaded file.
	 *
	 * @param  string   $path
	 * @return string
	 */
	public function uploadedFile($path = '')
	{
		return $this->rootUrl($this->get('uploadsPath').'/'.$path);
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
	public function selectForMatch($item, $itemToCompare, $inClass = false, $className = null)
	{
		$matches = false;
		if (is_array($itemToCompare))
		{
			if (in_array($item, $itemToCompare))
				$matches = true;
		} else {
			if ($item == $itemToCompare)
				$matches = true;
		}

		if ($matches)
			return $this->selectedHtml($inClass, $className);

		return null;
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
	public function selectBy($item = 'section', $itemToCompare = '', $inClass = false, $className = null)
	{
		return $this->selectForMatch($this->get($item), $itemToCompare, $inClass, $className);
	}

	/**
	 * Adds the "selected" class to an HTML element if all declared config items match their respective comparison variables (associative array).
	 *
	 * @param  array    $comparisonData
	 * @param  boolean  $inClass
	 * @param  mixed    $className
	 * @return string
	 */
	public function selectByMulti($comparisonData = array(), $inClass = false, $className = null)
	{
		foreach ($comparisonData as $item => $itemToCompare)
		{
			if ($this->get($item) != $itemToCompare)
				return null;
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
	private function selectedHtml($inClass = false, $className = null)
	{
		if (!$className || !is_string($className) || $className == "")
			$className = $this->get('selected_class');

		if ($inClass)
			return ' '.$className;
		else
			return ' class="'.$className.'"';
	}

	/**
	 * Add an item to the breadcrumb trail.
	 *
	 * @param  string   $title
	 * @param  string   $uri
	 * @return void
	 */
	public function addTrailItem($title = '', $uri = null)
	{
		if ($title != "")
			$this->trailItems[] = (object) [
				'title' => $title,
				'uri'   => $uri,
			];
	}

	/**
	 * Add items to the breadcrumb trail.
	 *
	 * @param  array    $items
	 * @return void
	 */
	public function addTrailItems($items)
	{
		foreach ($items as $title => $uri) {
			$this->addTrailItem($title, $uri);
		}
	}

	/**
	 * Get the breadcrumb trail items.
	 *
	 * @return array
	 */
	public function getTrailItems()
	{
		return $this->trailItems;
	}

	/**
	 * Reset the breadcrumb trail items.
	 *
	 * @return void
	 */
	public function resetTrailItems()
	{
		$this->trailItems = [];
	}

	/**
	 * Create HTML for breadcrumb trail.
	 *
	 * @param  mixed    $id
	 * @return string
	 */
	public function getBreadcrumbTrailMarkup($id = null)
	{
		$html = '';
		if (is_null($id))
			$id = $this->get('trailId');

		if (!empty($this->trailItems))
		{
			$html  = '<ol class="breadcrumb"'.(!is_null($id) ? ' id="'.$id.'"' : '').'>'."\n";

			foreach ($this->trailItems as $i => $item)
			{
				$html .= '<li'.($i + 1 == count($this->trailItems) ? ' class="active"' : '').'>';

				if (!is_null($item->uri))
				{
					if (substr($item->uri, 0, 7) == "http://" || substr($item->uri, 0, 8) == "https://")
						$url = $item->uri;
					else
						$url = URL::to($item->uri);

					$html .= '<a href="'.$url.'">'.Format::entities($item->title).'</a>';
				} else {
					$html .= Format::entities($item->title);
				}

				$html .= '</li>'."\n";
			}

			$html .= '</ol>'."\n";
		}

		return $html;
	}

	/**
	 * Add a button to the button list.
	 *
	 * @param  mixed    $label
	 * @param  string   $uri
	 * @return void
	 */
	public function addButton($label = '', $uri = null)
	{
		$button = [
			'label' => null,
			'uri'   => null,
			'url'   => null,
			'icon'  => null,
			'class' => null,
			'id'    => null,
		];

		if (is_array($label)) {
			$button = array_merge($button, $label);
		} else {
			$button['label'] = $label;
			$button['uri']   = $uri;
		}

		if (!is_null($button['label']) && $button['label'] != "")
			$this->buttons[] = (object) $button;
	}

	/**
	 * Add buttons to the button list.
	 *
	 * @param  array    $buttons
	 * @return void
	 */
	public function addButtons($buttons)
	{
		foreach ($buttons as $button) {
			$this->addButton($button);
		}
	}

	/**
	 * Get the button list.
	 *
	 * @return array
	 */
	public function getButtons()
	{
		return $this->buttons;
	}

	/**
	 * Reset the button list.
	 *
	 * @return void
	 */
	public function resetButtons()
	{
		$this->buttons = [];
	}

	/**
	 * Create HTML for button list.
	 *
	 * @param  mixed    $class
	 * @return string
	 */
	public function getButtonListMarkup($class = 'button-group')
	{
		$html = '';

		if (!empty($this->buttons))
		{
			$html  = '<div class="'.trim($class).'">'."\n";

			foreach ($this->buttons as $button)
			{
				$tag = (!is_null($button->uri) || !is_null($button->url) ? 'a' : 'button');

				$html .= '<'.$tag;

				if (!is_null($button->uri)) {
					$html .= ' href="'.URL::to($button->uri).'"';
				} else {
					if (!is_null($button->url))
						$html .= ' href="'.$button->url.'" target="_blank"';
				}

				if (!is_null($button->class))
					$html .= ' class="'.$class.'"';
				else
					$html .= ' class="btn btn-default"';

				if (!is_null($button->id))
					$html .= ' id="'.$id.'"';

				$html .= '>';

				if (!is_null($button->icon))
					$html .= '<span class="'.$button->icon.'"></span> ';

				$html .= Format::entities($button->label).'</'.$tag.'>'."\n";
			}

			$html .= '</div>'."\n";
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