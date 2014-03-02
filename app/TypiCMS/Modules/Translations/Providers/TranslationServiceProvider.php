<?php namespace TypiCMS\Modules\Translations\Providers;

use App;

use Illuminate\Translation\TranslationServiceProvider as LaravelTranslationServiceProvider;

// Loaders
use Illuminate\Translation\FileLoader;

// Loaders Custom
use TypiCMS\Modules\Translations\Loaders\MixedLoader;
use TypiCMS\Modules\Translations\Loaders\DatabaseLoader;

class TranslationServiceProvider extends LaravelTranslationServiceProvider {

	/**
	 * Register the translation line loader.
	 *
	 * @return void
	 */
	protected function registerLoader()
	{
		$this->app->bindShared('translation.loader', function($app)
		{
			// dd($app);
			$repository     = $app->make('TypiCMS\Modules\Translations\Repositories\TranslationInterface');
			$fileLoader     = new FileLoader($app['files'], $app['path'].'/lang');
			$databaseLoader = new DatabaseLoader($repository);
			return new MixedLoader($fileLoader, $databaseLoader);
		});
	}

}