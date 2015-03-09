<?php

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
|
| A number of helper functions are available for various things.
|
*/

if ( ! function_exists('assets_path'))
{
	/**
	 * Get a language item from language arrays.
	 *
	 * @param  string   $key
	 * @param  array    $replace
	 * @param  mixed    $locale
	 * @return string
	 */
	function assets_path($path = '', $fromAppDir = true)
	{
		return \Site::assetsPath($path, $fromAppDir);
	}
}

if ( ! function_exists('asset_url'))
{
	/**
	 * Create a URL for an asset.
	 *
	 * @param  string   $path
	 * @param  boolean  $secure
	 * @param  mixed    $package
	 * @param  mixed    $useRoot
	 * @return string
	 */
	function asset_url($path = '', $secure = false, $package = false, $useRoot = null)
	{
		return \Site::asset($path, $secure, $package, $useRoot);
	}
}