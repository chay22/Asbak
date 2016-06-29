<?php

namespace Chay22\Asbak\Services;

use Chay22\Asbak\Asbak;
use Chay22\Asbak\Repositories\CDN;
use Chay22\Asbak\Repositories\Identifier;
use Chay22\Asbak\Contract\Repository;

class AssetsParser
{
	/**
	 * Store instance of Asbak
	 * 
	 * @var \Chay22\Asbak\Asbak
	 */
	protected $assets;

	/**
	 * Local assets file path.
	 * 
	 * @var string
	 */
	protected $file;

	
	private $data = [
	  	'name' => null,
	  	'library' => null,
	  	'version' => null,
	  	'extension' => null,
	  	'identifier' => null,
	  	'cdn' => null,
	  	'fallback' => null,
	  	'min' => null,
	];
	/**
	 * Begin initiation.
	 * 
	 * @return void
	 */
	public function __construct(Asbak $assets) {
		$this->assets = $assets;
	}

	/**
	 * Set requested local asset and nullify previous data.
	 * 
	 * @param  string  $file  Local asset file with its path.
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 */
	public function js($file)
	{
		if (is_array($file)) {
			foreach ($file as $key => $val) {
				if (! array_key_exists($key, $this->data)) {
					throw new \Chay22\Asbak\Exceptions\NotFoundException(
						"Parameter '{$key}' doesn't exists."
					);
				}
				call_user_func([$this, $key], $val);
			}

			if (! array_key_exists('fallback', $file)) {
				throw new \Chay22\Asbak\Exceptions\NotFoundException(
					"Parameter 'fallback' needs to be exists."
				);
			}

			$this->file = $file['fallback'];

			return $this->assets;
		}

		$this->file = $file;
		
		foreach ($this->data as $key => $data) {
			$this->data[$key] = null;
		}
		
		return $this->assets;
	}

	/**
	 * Set exact file name that going to be turned into CDN.
	 * 
	 * @param  string  $name  File name
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 */
	public function name($name)
	{
		$this->data['name'] = $name;
		
		return $this->assets;
	}

	/**
	 * Set library name explicitly.
	 * 
	 * @param  string  $name  Library name
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 */
	public function library($name)
	{
		$this->data['library'] = strtolower($name);

		return $this->assets;
	}

	/**
	 * Set asset's version. This is useful part to help
	 * the weakness of version detection that only accept
	 * version number like "1.2.33" but would fail if it's
	 * like "1.2-rc.22", "r46", "v1.0" (contains alphabet)
	 * 
	 * @param  string  $version  Version number to set.
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 */
	public function version($version)
	{
		$this->data['version'] = $version;

		return $this->assets;
	}

	/**
	 * Set file extension
	 * 
	 * @param  string  $extension  js|css  
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 */
	public function extension($extension)
	{
		$this->data['extension'] = strtolower($extension);

		return $this->assets;
	}

	/**
	 * Set CDN that going to be used. If it contains URL, all CDN
	 * detection would be omitted and use its CDN instead. And it's
	 * better since detection may fail in any cases.
	 * 
	 * @param  string  $cdn  URL of CDN file|CDN key from \Repository class
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 */
	public function cdn($cdn)
	{
		$this->data['cdn'] = $cdn;

		return $this->assets;
	}

	/**
	 * Whether set CDN file to be minified or no.
	 * 
	 * @param  bool  $minified
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 * @throws \Chay22\Asbak\Exceptions\WrongDataException
	 */
	public function min($minified = true)
	{
		if (!is_bool($minified)) {
			throw new \Chay22\Asbak\Exceptions\WrongDataException(
				"$minified must be a type of boolean"
			);
		}

		$this->data['minified'] = $minified;

		return $this->assets;
	}

	/**
	 * Explicitly set a fallback if array given on js() method
	 * 
	 * @param  string  $file    Path to local file
	 * 
	 * @return void
	 */
	private function fallback($file)
	{
		$this->data['fallback'] = $file;
	}

	/**
	 * Get all data
	 * 
	 * @return array
	 */
	public function get()
	{
		return $this->data;
	}

	/**
	 * Autoload $data
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->data)) {
		    return $this->data[$key];
		}

		throw new \Chay22\Asbak\Exceptions\NotFoundException("{$key} not found");
	}

}