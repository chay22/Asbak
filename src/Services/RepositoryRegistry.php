<?php

namespace Chay22\Asbak\Services;

use Chay22\Asbak\Repositories\CDN;
use Chay22\Asbak\Repositories\Identifier;
use Chay22\Asbak\Contracts\Repository as RepositoryContract;

class RepositoryRegistry
{	
	/**
	 * Default CDN
	 * 
	 * @var string
	 */
	private $defaultCdn = 'cloudflare';

	/**
	 * Bootstrap repositoy classes.
	 * 
	 * @var array
	 */
	private $repository = [
		'\Chay22\Asbak\Repositories\CDN',
		'\Chay22\Asbak\Repositories\Identifier',
	];

	/**
	 * Begin initiation.
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->registerRepository();
	}

	/**
	 * Get file name from local file and its path
	 * 
	 * @param  string  $file  File and its path
	 * 
	 * @return string
	 */
	public function getName($file)
	{
		$filename = basename($file);
		$ext = self::getExtension($file);
		$name = explode('.' . $ext, $filename);

		if (stripos($filename, '.min.')) {
			$name = explode('.min.', $filename);
		}

		return $name[0];
	}

	/**
	 * Get version from local file and its path
	 * 
	 * @param  string  $file  File and its path
	 * 
	 * @return string
	 */
	public function getVersion($file)
	{
		$version = preg_match('/\d+(?:\.\d+)+/', $file, $matches);
		return $matches[0];
	}

	/**
	 * Get fallback identifier from file and its path
	 * 
	 * @param  string  $file  File and its path
	 * 
	 * @return string
	 * @throws \Chay22\Asbak\Exceptions\NotFoundException;
	 */
	public function getIdentifier($file)
	{
		$identifier = $this->repository('identifier')
						   ->get($this->getLibrary($file));
		
		if (! is_null($identifier)) {
			return $identifier;
		} else {
			return $this->repository('identifier')
						->get($this->getName($file));
		}

		throw new \Chay22\Asbak\Exceptions\NotFoundException(
			"Could not find any matches identifier from {$file}. " .
			"Please define it manually with identifier() method."
		);
	}

	/**
	 * Get file extension from file and its path
	 * 
	 * @param  string  $file  File and its path
	 * 
	 * @return string
	 */
	public function getExtension($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}

	/**
	 * Get CDN from \Repository class
	 * 
	 * @param  string  $cdn  CDN key
	 * 
	 * @return  string
	 */
	public function getCdn($cdn = null)
	{
		$repoCdn = $this->repository('cdn')->get($cdn);
		if (! is_null($cdn) && ! is_null($repoCdn)) {
			return strtolower($cdn);
		}

		return $this->defaultCdn;
	}

	/**
	 * Get library name from local file
	 * 
	 * @param  string  $file  Current local file
	 * 
	 * @return  string
	 */
	public function getLibrary($file)
	{
		return array_slice(explode('/', $file), -3, 1)[0];
	}

	/**
	 * Get data from \Repository class
	 * 
	 * @param  string  $repo 	 Repository key name
	 * @param  string  $list     Data key of value to retrieve
	 * 
	 * @return string
	 */
	private function getRepository($repo, $list = null)
	{
		return $this->repository->{$repo}->get($list);
	}

	/**
	 * Return specific repository instances
	 *
	 * @param  string  $repo 	Repository key name
	 *
	 * @return classes implement \Chay22\Asbak\Contracts\Repository
	 */
	public function repository($repo)
	{
		return $this->repository[$repo];
	}

	/**
	 * Get default selected repository data
	 * 
	 * @param  string  $repo  Repository key name
	 * 
	 * @return  string
	 */
	private function getDefaultRepository($repo)
	{
		return $this->repository[$repo]->getDefault();
	}

	/**
	 * Dispatch registered \Repository classes
	 * 
	 * @return collection
	 * @throws \Chay22\Asbak\Exceptions\WrongDataException;
	 */
	private function registerRepository()
	{
		$repositories = $this->repository;

		foreach ($repositories as $repo) {
			$instance = new $repo();
			if (! $instance instanceof RepositoryContract) {
				throw new \Chay22\Asbak\Exceptions\WrongDataException(
					"Class must be an instance of " .
					"\Chay22\Asbak\Contract\Repository interface," .
					"{$repo} given."
				);
			}

			$key = strtolower(substr(strrchr($repo, '\\'), 1));
			$newRepo[$key] = $instance;
			unset($repositories);
		}

		$this->repository = $newRepo;
	}

	/**
	 * Set default CDN
	 * 
	 * @param  string  $value  CDN key to set as default
	 * 
	 * @return void
	 */
	public function setDefaultCdn($value)
	{
		$this->defaultCdn = $value;
	}

	/**
	 * Autoload $repository
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->repository)) {
		    return $this->repository[$key];
		}

		throw new \Chay22\Asbak\Exceptions\NotFoundException("{$key} not found");
	}
}
