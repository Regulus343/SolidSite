<?php namespace Regulus\SolidSite;

/*----------------------------------------------------------------------------------------------------------
	SolidSite
		A Laravel 5 composer package that assigns section names & titles to pages, simplifies creation of
		breadcrumb trails, pagination, and other components.

		created by Cody Jassman
		v0.8.1
		last updated on April 20, 2021
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;

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
	 * @var    string
	 */
	public $buttonList = "main";

	/**
	 * Get a config item.
	 *
	 * @param  string   $item
	 * @param  mixed    $default
	 * @return mixed
	 */
	public function get($item, $default = null)
	{
		return Config::get('site.'.Str::snake($item), $default);
	}

	/**
	 * Set a config item or multiple config items.
	 *
	 * @param  mixed    $item
	 * @param  mixed    $value
	 * @return mixed
	 */
	public function set($item, $value = null)
	{
		if (is_array($item))
		{
			$items       = $item;
			$associative = array_keys($items) !== range(0, count($items) - 1);

			if ($associative)
			{
				foreach ($items as $item => $value)
				{
					Config::set('site.'.Str::snake($item), $value);
				}
			}
			else
			{
				foreach ($items as $item)
				{
					Config::set('site.'.Str::snake($item), $value);
				}
			}
		}
		else
		{
			Config::set('site.'.Str::snake($item), $value);
		}

		return $value;
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
	 * Get a website email.
	 *
	 * @param  mixed    $type
	 * @return string
	 */
	public function email($type = null)
	{
		if (is_null($type))
			$type = "admin";

		if ($this->get('email.testMode'))
			$type = "test";

		$email = $this->get('email.addresses.'.camel_case($type));

		if (!is_null($email) && strpos($email, '@') === false)
			$email .= '@'.str_replace('https://', '', str_replace('http://', '', config('app.url')));

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

		if (is_null($title) || $title == "")
		{
			return $this->get('name');
		}
		else
		{
			$titlePrefix = $this->get('title.prefix');

			if (!is_null($titlePrefix))
				$title = $titlePrefix.': '.$title;

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
			$title = $this->entities($title);

		$titlePrefix = $this->get('title.prefixHeading');

		if (!is_null($titlePrefix) && $title != "")
			$title = $titlePrefix.': '.$title;

		return $title;
	}

	/**
	 * Get the sub-heading title. If no specific sub-heading title is set, the main heading or regular title is used.
	 *
	 * @param  boolean  $useSiteName
	 * @return string
	 */
	public function subHeading($useSiteName = false)
	{
		$title = $this->get('title.subHeading');

		if (is_null($title) || $title == "" || !is_string($title))
			$title = $this->get('title.heading');

		if (is_null($title) || $title == "" || !is_string($title))
			$title = $this->get('title.main');

		if ((is_null($title) || $title == "" || !is_string($title)) && $useSiteName)
			$title = $this->name();

		if (!is_string($title))
			$title = "";

		if (strip_tags($title) == $title)
			$title = $this->entities($title);

		return $title;
	}

	/**
	 * Set the page title prefix.
	 *
	 * @param  string   $item
	 * @param  mixed    $value
	 * @param  boolean  $setForHeading
	 * @return mixed
	 */
	public function setTitlePrefix($value = null, $setForHeading = false)
	{
		$this->set('title.prefix', $value);

		if ($setForHeading)
			$this->set('title.prefixHeading', $value);

		return $value;
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
	 * Set the page sub-heading.
	 *
	 * @param  string   $item
	 * @param  mixed    $value
	 * @return mixed
	 */
	public function setSubHeading($value = null)
	{
		$this->set('title.subHeading', $value);

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

		$baseUrl = str_replace('https://', '', str_replace('http://', '', config('app.url')));

		if ($subdomain !== true)
		{
			$escapedBaseUrl = str_replace('.', '\.', str_replace('/', '\/', str_replace('-', '\-', str_replace('~', '\~', $baseUrl))));

			// remove subdomain if one exists
			$url = preg_replace('/(http[s]?:\/\/)[A-Za-z0-9]*[\.]?('.$escapedBaseUrl.')/', '${1}${2}', $url);

			// add subdomain if one is set
			if ($subdomain != "" && $subdomain !== false && !is_null($subdomain))
				$url = preg_replace('/(http[s]?:\/\/)('.$escapedBaseUrl.')/', '${1}'.$subdomain.'.${2}', $url);
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
		return base_path().DIRECTORY_SEPARATOR.$this->get('paths.public');
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
		$path = ($fromAppDir ? $this->publicPath() : $this->get('paths.root'));

		if (is_null($path) || $path == "" || !$path)
			$path = $this->get('paths.assets');
		else
			$path .= '/'.$this->get('paths.assets');

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
	 * @param  mixed    $package
	 * @param  mixed    $useRoot
	 * @return string
	 */
	public function asset($path = '', $package = false, $useRoot = null)
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
		// if no extension is given, assume .png
		if ($addExtension && $path != "" && !in_array(File::extension($path), ['png', 'jpg', 'jpeg', 'jpe', 'gif', 'ico', 'svg']))
			$path .= ".png";

		$path = $this->getDirectoryForPath($this->get('paths.images').'/'.$path, $package);

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
		// add .css extension if one doesn't exist
		if ($path != "" && File::extension($path) != "css")
			$path .= ".css";

		$path = $this->getDirectoryForPath($this->get('paths.css').'/'.$path, $package);

		if (is_null($useRoot))
			$useRoot = $this->get('useRoot');

		return $useRoot ? $this->rootUrl($path) : $this->url($path, true);
	}

	/**
	 * Create a URL for a JavaScript file.
	 *
	 * @param  string   $path
	 * @param  mixed    $package
	 * @param  mixed    $useRoot
	 * @return string
	 */
	public function js($path = '', $package = false, $useRoot = null)
	{
		// add .js extension if one doesn't exist
		if ($path != "" && File::extension($path) != "js")
			$path .= ".js";

		$path = $this->getDirectoryForPath($this->get('paths.js').'/'.$path, $package);

		if (is_null($useRoot))
			$useRoot = $this->get('useRoot');

		return $useRoot ? $this->rootUrl($path) : $this->url($path, true);
	}

	/**
	 * Get the contents of an SVG file.
	 *
	 * @param  string   $path
	 * @param  mixed    $viewBoxDimensions
	 * @param  boolean  $absolutePath
	 * @param  mixed    $package
	 * @return string
	 */
	public function svg($path = '', $viewBoxDimensions = null, $absolutePath = false, $package = false)
	{
		$id = null;
		if (strpos($path, '#') !== false)
		{
			$pathArray = explode('#', $path);

			$path = $pathArray[0];
			$id   = $pathArray[1];
		}

		// if no extension is given, add .svg
		if (is_null($id) || $path != "")
		{
			if ($path != "" && File::extension($path) != "svg")
				$path .= ".svg";

			$svgPath = $this->get('paths.svg');
			if ($svgPath != "" && $svgPath != false && !is_null($svgPath))
				$path = $svgPath.'/'.$path;

			$path = $this->getDirectoryForPath($this->get('paths.images').'/'.$path, $package);
		}

		// use external source instead of inline to allow browser caching
		if (!is_null($id) && !is_null($viewBoxDimensions))
		{
			if ($path != "")
			{
				if ($absolutePath)
				{
					$path = $this->rootUrl($absolutePath);
				}
				else
				{
					if (substr($path, 0, 1) != "/")
						$path = '/'.$path;
				}
			}

			if (is_array($viewBoxDimensions))
			{
				switch (count($viewBoxDimensions))
				{
					case 1: $strViewBox = "0 0 ".$viewBoxDimensions[0]." ".$viewBoxDimensions[0]; break;
					case 2: $strViewBox = "0 0 ".$viewBoxDimensions[0]." ".$viewBoxDimensions[1]; break;
					case 3: $strViewBox = "0 ".$viewBoxDimensions[0]." ".$viewBoxDimensions[1]." ".$viewBoxDimensions[2]; break;
					case 4: $strViewBox = $viewBoxDimensions[0]." ".$viewBoxDimensions[1]." ".$viewBoxDimensions[2]." ".$viewBoxDimensions[3]; break;
				}
			}
			else
			{
				$strViewBox = "0 0 ".$viewBoxDimensions." ".$viewBoxDimensions;
			}

			return '<svg viewBox="'.$strViewBox.'"><use xlink:href="'.$path.'#'.$id.'"></use></svg>';
		}

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
		$uploadsPath = $this->get('paths.uploads');

		if (substr($path, 0, strlen($uploadsPath)) != $uploadsPath)
			$path = $uploadsPath.'/'.$path;

		return $this->rootUrl($path);
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
		}
		else
		{
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
	 * @return mixed
	 */
	public function selectByMulti($comparisonData = [], $inClass = false, $className = null)
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
			$className = $this->get('selectedClass');

		if ($inClass)
			return ' '.$className;
		else
			return ' class="'.$className.'"';
	}

	/**
	 * Add an item to the breadcrumb trail.
	 *
	 * @param  string   $title
	 * @param  mixed    $uri
	 * @param  boolean  $class
	 * @return void
	 */
	public function addTrailItem($title = '', $uri = null, $class = null)
	{
		if ($title == "")
			return false;

		if (is_null($uri) || (is_string($uri) && (substr($uri, 0, 7) == "http://" || substr($uri, 0, 8) == "https://")))
		{
			$url = $uri;
		}
		else
		{
			if (is_array($uri))
			{
				$url = $this->url($uri[0], $uri[1]);
				$uri = $uri[0];
			}
			else
			{
				$url = $this->url($uri);
			}
		}

		$this->trailItems[] = (object) [
			'title' => $title,
			'uri'   => $uri,
			'url'   => $url,
			'class' => $class,
		];

		return true;
	}

	/**
	 * Add items to the breadcrumb trail.
	 *
	 * @param  array    $items
	 * @return void
	 */
	public function addTrailItems($items)
	{
		foreach ($items as $title => $uri)
		{
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
	 * Reset the breadcrumb trail items.
	 *
	 * @param  object   $item
	 * @param  mixed    $index
	 * @param  boolean  $inClass
	 * @return string
	 */
	public function getTrailItemClass($item, $index = null, $inClass = false)
	{
		$class = $item->class;

		if (!is_null($index) && $index + 1 == count($this->trailItems))
		{
			if (is_null($class))
				$class = "";
			else
				$class .= " ";

			$class .= "active";
		}

		if (is_null($class))
			return "";

		if ($inClass)
			return ' '.$class;
		else
			return ' class="'.$class.'"';
	}

	/**
	 * Create HTML for breadcrumb trail.
	 *
	 * @param  mixed    $id
	 * @return string
	 */
	public function getBreadcrumbTrailMarkup($id = null)
	{
		if (is_null($id))
			$id = $this->get('trail.id');

		return view('solid-site::breadcrumb_trail', [
			'trailId'    => $id,
			'trailItems' => $this->trailItems,
		])->render();
	}

	/**
	 * Add a button to a button list.
	 *
	 * @param  mixed    $label
	 * @param  string   $uri
	 * @return void
	 */
	public function addButton($label = '', $uri = null)
	{
		$button = [
			'tag'   => null,
			'label' => null,
			'uri'   => null,
			'url'   => null,
			'icon'  => null,
			'class' => null,
			'id'    => null,
		];

		if (is_array($label))
		{
			$button = array_merge($button, $label);
		}
		else
		{
			$button['label'] = $label;
			$button['uri']   = $uri;
		}

		if (isset($button['list']))
			$this->setButtonList($button['list']);

		if (is_null($button['url']) && !is_null($button['uri']))
		{
			$button['url'] = $this->url($button['uri']);
		}
		else
		{
			if (is_array($button['url']))
				$button['url'] = $this->url($button['url'][0], $button['url'][1]);
		}

		if (is_null($button['tag']))
			$button['tag'] = !is_null($button['url']) ? 'a' : 'button';

		if (is_null($button['class']) || $button['class'] == "")
		{
			$button['class'] = $this->get('buttons.defaultClass');
		}
		else
		{
			if ($this->get('buttons.defaultClassAlwaysPresent'))
				$button['class'] = $this->get('buttons.defaultClass').' '.$button['class'];
		}

		if (!is_null($button['label']) && $button['label'] != "")
			$this->buttons[$this->buttonList][] = (object) $button;
	}

	/**
	 * Add buttons to a button list.
	 *
	 * @param  array    $buttons
	 * @param  mixed    $list
	 * @return void
	 */
	public function addButtons($buttons, $list = null)
	{
		foreach ($buttons as $button)
		{
			if (!is_null($list))
				$button['list'] = $list;

			$this->addButton($button);
		}
	}

	/**
	 * Get a button list.
	 *
	 * @param  mixed    $list
	 * @return array
	 */
	public function getButtons($list = null)
	{
		if (is_null($list))
			$list = $this->buttonList;

		return isset($this->buttons[$list]) ? $this->buttons[$list] : [];
	}

	/**
	 * Set the active button list.
	 *
	 * @param  string    $list
	 * @return void
	 */
	public function setButtonList($list)
	{
		$this->buttonList = $list;
	}

	/**
	 * Reset a button list.
	 *
	 * @param  mixed    $list
	 * @return void
	 */
	public function resetButtons($list = null)
	{
		if (is_null($list))
			$this->buttons = [];
		else
			$this->buttons[$list] = [];
	}

	/**
	 * Get the HTML attributes for a button.
	 *
	 * @param  object   $button
	 * @return string
	 */
	public function getButtonAttributes($button)
	{
		$attributes = "";

		$ignoredAttributes = ['tag', 'uri', 'label'];
		foreach ($button as $attribute => $value)
		{
			if (!in_array($attribute, $ignoredAttributes) && !is_null($value))
			{
				if ($attributes != "")
					$attributes .= " ";

				if ($attribute == "url")
				{
					$attributes .= ($button->tag == "a" ? 'href' : 'data-url').'="'.$value.'"';
				}
				else
				{
					$attributes .= $attribute.'="'.$value.'"';
				}
			}
		}

		return $attributes;
	}

	/**
	 * Create HTML for button list.
	 *
	 * @param  mixed    $data
	 * @return string
	 */
	public function getButtonListMarkup($data = null)
	{
		if (is_string($data))
			$data = [
				'list' => $data,
			];

		if (!isset($data['list']))
			$data['list'] = $this->buttonList;

		if (!isset($data['id']))
			$data['id'] = "button-list-".strtolower(str_replace(' ', '-', $data['list']));

		if (!isset($data['class']))
			$data['class'] = $this->get('buttons.defaultListClass');

		$data['buttons'] = $this->getButtons($data['list']);

		$view = isset($data['view']) ? $data['view'] : "solid-site::button_list";

		return view($view, $data)->render();
	}

	/**
	 * Get paginated items (intended for use with pagination that does not use GET query strings).
	 *
	 * @param  QueryBuilder  $query
	 * @param  mixed    $page
	 * @return Collection
	 */
	public function paginate($query, $page = null, $config = [])
	{
		$rawQuery = $query;

		if (!isset($config['itemsPerPage']))
		{
			$config['itemsPerPage'] = $this->get('pagination.itemsPerPage', 25);
		}

		if (!isset($config['preventArray']))
		{
			$config['preventArray'] = $this->get('pagination.preventArray', false);
		}

		if (!isset($config['attributeSet']))
		{
			$config['attributeSet'] = $this->get('pagination.defaultAttributeSet', false);
		}

		if (!isset($config['camelizeArrayKeys']))
		{
			$config['camelizeArrayKeys'] = $this->get('pagination.camelizeArrayKeys', config('form.camelize_array_keys', false));
		}

		if (is_array($page) && empty($config))
		{
			$config = $page;
			$page   = isset($config['page']) ? $config['page'] : null;
		}

		if (!in_array($page, ['first', 'last']))
		{
			if (is_null($page))
				$page = Input::get('page');

			$page = (int) $page;
			if (!$page)
				$page = 1;
		}
		else
		{
			$items = $rawQuery->paginate($config['itemsPerPage']);

			switch ($page)
			{
				case "first": $page = 1; break;
				case "last":  $page = $items->lastPage(); break;
			}
		}

		if (!is_null($page))
		{
			Paginator::currentPageResolver(function() use ($page)
			{
				return $page;
			});
		}

		$paginated = $query->paginate($config['itemsPerPage']);

		$items = $paginated->getCollection();

		$attributeSetApplied = !is_null($config['attributeSet']) && $items->count();

		if ($attributeSetApplied)
		{
			$model = get_class($items[0]);

			if (method_exists($model, 'collectionToLimitedArray'))
			{
				$items = $model::collectionToLimitedArray($items, $config['attributeSet']);
			}
		}

		if (isset($config['uri']) && !isset($config['url']))
			$config['url'] = $this->url($config['uri']);

		if (isset($config['url']) && is_array($config['url']))
			$config['url'] = $this->url($config['url'][0], $config['url'][1]);

		foreach ($config as $item => $value)
		{
			if ($item != "preventArray")
				$this->set('pagination.'.$item, $value);

			if ($item == "url")
				$paginated->setPath($value);
		}

		$this->set('pagination.page', $page);
		$this->set('pagination.lastPage', $paginated->lastPage());
		$this->set('pagination.items', $items);

		if (!$config['preventArray'] && ($config['camelizeArrayKeys'] || $attributeSetApplied))
		{
			$paginated = $paginated->toArray();

			if ($attributeSetApplied)
			{
				unset($paginated['data']);
			}

			if ($config['camelizeArrayKeys'] && class_exists('Regulus\TetraText\Facade'))
			{
				$paginated = \Regulus\TetraText\Facade::camelizeKeys($paginated);
			}

			if ($attributeSetApplied)
			{
				$paginated['data'] = $items;
			}
		}

		return $paginated;
	}

	/**
	 * Set the pagination parameters for GET query links.
	 *
	 * @param  array    $parameters
	 * @return void
	 */
	public function setPaginationParameters(array $parameters = [])
	{
		$this->set('pagination.parameters', $parameters);
	}

	/**
	 * Create HTML for pagination.
	 *
	 * @param  mixed    $items
	 * @param  mixed    $parameters
	 * @param  boolean  $inner
	 * @return string
	 */
	public function getPaginationMarkup($items = null, $parameters = null, $inner = false)
	{
		if (is_null($items))
			$items = $this->get('pagination.items', []);

		if (!is_null($parameters))
		{
			ksort($parameters);

			$this->set('pagination.parameters', $parameters);
		}

		$html = view('solid-site::pagination'.($inner ? '_pages' : ''), ['items' => $items])->render();

		$this->set('pagination.uiAdded', true);

		return $html;
	}

	/**
	 * Create inner HTML for pagination.
	 *
	 * @param  mixed    $items
	 * @param  mixed    $parameters
	 * @return string
	 */
	public function getInnerPaginationMarkup($items = null, $parameters = null)
	{
		return $this->getPaginationMarkup($items, $parameters, true);
	}

	/**
	 * Get the class attribute for a page link.
	 *
	 * @param  mixed    $page
	 * @param  mixed    $items
	 * @return integer
	 */
	public function getPageNumber($page, $items = null)
	{
		if (is_null($items))
			$items = $this->get('pagination.items', []);

		switch ($page)
		{
			case "previous": $page = $items->currentPage() - 1; break;
			case "next":     $page = $items->currentPage() + 1; break;
			case "first":    $page = 1; break;
			case "last":     $page = $items->lastPage(); break;
		}

		if ($page < 1)
			$page = 1;

		if ($page > $items->lastPage())
			$page = $items->lastPage();

		return (int) $page;
	}

	/**
	 * Get the class attribute for a page link.
	 *
	 * @param  mixed    $page
	 * @param  mixed    $items
	 * @return mixed
	 */
	public function getPageLink($page, $items = null)
	{
		if (is_null($items))
			$items = $this->get('pagination.items', []);

		if (!$this->get('pagination.pageLinks.href.enabled'))
			return null;

		$page = $this->getPageNumber($page, $items);

		if ($this->get('pagination.pageLinks.href.get'))
		{
			$parameters = $this->get('pagination.parameters');
			$parameters['page'] = $page;

			$url = $this->get('pagination.url');

			return $url.(Str::contains($url, '?') ? '&' : '?').http_build_query($parameters, null, '&');
		}
		else
		{
			return $this->get('pagination.url').'/'.$page;
		}
	}

	/**
	 * Get the class attribute for a page link.
	 *
	 * @param  mixed    $page
	 * @param  mixed    $items
	 * @return string
	 */
	public function getPageLinkClass($page, $items = null)
	{
		if (is_null($items))
			$items = $this->get('pagination.items', []);

		$class   = $this->get('pagination.classes.default');
		$classes = $this->get('pagination.classes');

		$special = in_array($page, ['previous', 'next', 'separator']);

		if ($special)
		{
			if ($class != "")
				$class .= " ";

			$class .= $page;
		}

		if ($items->currentPage() == $page && !$special)
		{
			if (!is_null($class) && $class != "" && isset($classes['active']) && !is_null($classes['active']) && $classes['active'] != "")
				$class .= " ";

			$class .= $classes['active'];
		}
		else
		{
			if (!is_null($class) && $class != "" && isset($classes['inactive']) && !is_null($classes['inactive']) && $classes['inactive'] != "")
				$class .= " ";

			$class .= $classes['inactive'];
		}

		if ($page == "separator" || ($page == "previous" && $items->currentPage() == 1) || ($page == "next" && $items->currentPage() == $items->lastPage()))
		{
			if (!is_null($class) && $class != "" && isset($classes['disabled']) && !is_null($classes['disabled']) && $classes['disabled'] != "")
				$class .= " ";

			$class .= $classes['disabled'];
		}

		return $class;
	}

	/**
	 * Set the user to "developer" status.
	 *
	 * @return void
	 */
	public function setDeveloper()
	{
		Session::put('developer', true);
	}

	/**
	 * Get the user's "developer" status based on session variable.
	 *
	 * @return boolean
	 */
	public function developer()
	{
		$developer = Session::get('developer');

		return !is_null($developer) && $developer;
	}

	/**
	 * Convert HTML characters to entities.
	 *
	 * The encoding specified in the application configuration file will be used.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function entities($value)
	{
		$encoding = config('format.encoding');

		if (is_null($encoding))
			$encoding = "UTF-8";

		return htmlentities($value, ENT_QUOTES, config('format.encoding'), false);
	}

}