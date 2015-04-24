<?php namespace Waavi\Translation\Loaders;

use Illuminate\Translation\LoaderInterface;
use Waavi\Translation\Loaders\Loader;
use Waavi\Translation\Providers\LanguageProvider as LanguageProvider;
use Waavi\Translation\Providers\LanguageEntryProvider as LanguageEntryProvider;

class MixedLoader extends Loader implements LoaderInterface {

	/**
	 *	The file loader.
	 *	@var \Waavi\Translation\Loaders\FileLoader
	 */
	protected $fileLoader;

	/**
	 *	The database loader.
	 *	@var \Waavi\Translation\Loaders\DatabaseLoader
	 */
	protected $databaseLoader;

	/**
	 * 	Create a new mixed loader instance.
	 *
	 * 	@param  \Waavi\Lang\Providers\LanguageProvider  			$languageProvider
	 * 	@param 	\Waavi\Lang\Providers\LanguageEntryProvider		$languageEntryProvider
	 *	@param 	\Illuminate\Foundation\Application  					$app
	 */
	public function __construct($languageProvider, $languageEntryProvider, $app)
	{
		parent::__construct($languageProvider, $languageEntryProvider, $app);
		$this->fileLoader 		= new FileLoader($languageProvider, $languageEntryProvider, $app);
		$this->databaseLoader = new DatabaseLoader($languageProvider, $languageEntryProvider, $app);
	}

	/**
	 * Load the messages strictly for the given locale.
	 *
	 * @param  Language  	$language
	 * @param  string  		$group
	 * @param  string  		$namespace
	 * @return array
	 */
	public function loadRawLocale($locale, $group, $namespace = null)
	{
		$namespace = $namespace ?: '*';
		$app = $this->app;
		$precedence = $app['config']['translation.mixed_mode_precedence'];

		$db = $this->databaseLoader->loadRawLocale($locale, $group, $namespace);
		$fs = $this->fileLoader->loadRawLocale($locale, $group, $namespace);

		return $precedence == 'filesystem' ? array_replace_recursive($db, $fs) : array_replace_recursive($fs, $db);
	}
}