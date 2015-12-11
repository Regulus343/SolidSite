<?php namespace Regulus\SolidSite;

class Application extends \Illuminate\Foundation\Application {

	/**
	 * The relative path to the public directory from the application directory.
	 *
	 * @var    mixed
	 */
	protected $publicPath = null;

	/**
	 * Get the path to the public / web directory.
	 *
	 * @return string
	 */
	public function publicPath()
	{
		$siteConfigFile = config_path('site.php');
		if (is_null($this->publicPath) && is_file($siteConfigFile))
		{
			$siteConfig = require $siteConfigFile;

			if (isset($siteConfig['public_path']))
				$this->publicPath = $siteConfig['public_path'];
		}

		$publicPath = !is_null($this->publicPath) ? $this->publicPath : "public";

		return $this->basePath.DIRECTORY_SEPARATOR.$publicPath;
	}

}