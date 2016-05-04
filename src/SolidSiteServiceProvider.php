<?php namespace Regulus\SolidSite;

use Illuminate\Support\ServiceProvider;

class SolidSiteServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__.'/config/site.php' => config_path('site.php'),
			__DIR__.'/assets'          => assets_path('regulus/solid-site'),
			__DIR__.'/views'           => resource_path('views/vendor/solid-site'),
		]);

		$this->loadViewsFrom(__DIR__.'/views', 'solid-site');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('Regulus\SolidSite\SolidSite', function()
		{
			return new SolidSite;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['Regulus\SolidSite\SolidSite'];
	}

}