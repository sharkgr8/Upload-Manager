<?php namespace Shark\UploadManager;

use Illuminate\Support\ServiceProvider;

class UploadManagerServiceProvider extends ServiceProvider {

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
		$this->package('shark/upload-manager');
        require __DIR__.'/routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app['upload-manager'] = $this->app->share(function()
        {
            return new UploadManager;
        });

        $this->app->booting(function() {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('UploadManager', 'Shark\UploadManager\facades\UploadManager');
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('upload-manager');
	}

}
