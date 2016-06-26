<?php

namespace Chay22\Asbak;

use Chay22\Asbak\Registry\CDN;
use Chay22\Asbak\Registry\Identifier;
use Chay22\Asbak\Contract\Registry;

class Asbak
{
	/**
	 * Path to local assets.
	 * 
	 * @var string
	 */
	protected $file;

	/**
	 * Asset's file name.
	 * 
	 * @var string
	 */
	protected $name;

	/**
	 * Asset's library name.
	 * 
	 * @var string
	 */
	protected $library;

	/**
	 * Asset's version.
	 * 
	 * @var string
	 */
	protected $version;

	/**
	 * Type of file.
	 * 
	 * @var string  js|css
	 */
	protected $extension;

	/**
	 * Alias name of CDN or it's full URL.
	 * 
	 * @var string
	 */
	protected $cdn;

	/**
	 * Turn CDN into minified version or no.
	 * 
	 * @var boolean
	 */
	protected $minified;

	/**
	 * Bootstrap registry classes.
	 * 
	 * @var array
	 */
	protected $registry = [
		CDN::class,
		Identifier::class,
	];

	/**
	 * Store current script of CDN.
	 * 
	 * @var string
	 */
	protected static $script;

	/**
	 * Store current sript of fallback.
	 * 
	 * @var string
	 */
	protected static $fallback;

	/**
	 * Begin initiation.
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->registerRegistry();
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
		$this->file = $file;
		
		$this->name = null;
		
		$this->library = null;
		
		$this->version = null;
		
		$this->extension = null;
		
		$this->cdn = null;
		
		$this->minified = null;
		
		return $this;
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
		$this->name = $name;
		
		return $this;
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
		$this->library = $name;

		return $this;
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
		$this->version = $version;

		return $this;
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
		$this->extension = $extension;

		return $this;
	}

	/**
	 * Set CDN that going to be used. If it contains URL, all CDN
	 * detection would be omitted and use its CDN instead. And it's
	 * better since detection may fail in any cases.
	 * 
	 * @param  string  $cdn  URL of CDN file|CDN key from \Registry class
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 */
	public function cdn($cdn)
	{
		$this->cdn = $this->isUrl($cdn) ? $cdn : $this->getCdn($cdn);

		return $this;
	}

	/**
	 * Whether set CDN file to be minified or no.
	 * 
	 * @param  bool  $minified
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 * @throws \Exception
	 */
	public function min($minified = true)
	{
		if (!is_bool($minified)) {
			throw new \Exception("$minified must be a type of boolean");
		}

		$this->minified = $minified;

		return $this;
	}

	/**
	 * Print result. This is same as `echo`ing object of this class.
	 * 
	 * @return  string  Result of assets that'll printed to HTML.
	 */
	public function get()
	{
		echo $this;
	}

	/**
	 * Turn object of this class to string
	 */
	public function __toString()
	{
		return $this->wrap();
	}

	/**
	 * Wrap up all data to HTML ready assets
	 * 
	 * @return string
	 */
	public function wrap()
	{
		$fallback = $this->file;

		$name = isset($this->name)
			  ? $this->name : $this->getName($fallback);

		$library = isset($this->library)
			  ? $this->library : $this->getLibraryName($fallback);
		
		$version = isset($this->version)
				 ? $this->version : $this->getVersion($fallback);
		
		$extension = isset($this->extension)
				   ? $this->extension : $this->getExtension($fallback);
		
		$cdn = isset($this->cdn) && !empty($this->cdn)
			  ? $this->cdn : $this->getCdn();

		$minified = $this->isMinified($fallback);

		$identifier = $this->getIdentifier($fallback);

		$data = [
		  	'name' => $name,
		  	'library' => $library,
		  	'version' => $version,
		  	'extension' => $extension,
		  	'identifier' => $identifier,
		  	'cdn' => $cdn,
		  	'fallback' => $fallback,
		  	'min' => $minified,
		];

		array_walk($data, function(&$value, $key) {
			if ($key != 'version' && $key != 'identifier' && $key != 'name')
				 $value = strtolower($value);
		});

		return $this->script($data) . $this->fallback($data);
	}

	/**
	 * Turn data into CDN script
	 * 
	 * @param  array  $data
	 * 
	 * @return string
	 */
	protected function script($data)
	{	
		$url = $this->getRegistry('cdn', $data['cdn']);

		if (! is_null($url)) {
			$url = $this->extractRegistry('cdn', $data);
		} else {
			$url = $this->getRegistry('cdn', $this->getDefaultRegistry('cdn'));
			$url = $this->extractRegistry('cdn', $data);
		}

		return '<script type="text/javascript" src="'.$url.'"></script>'.PHP_EOL;
	}

	/**
	 * Turn data into fallback script
	 * 
	 * @param  array  $data
	 * 
	 * @return string
	 */
	protected function fallback($data)
	{
		$fallback = $data['fallback'];
		$identifier = $data['identifier'];

		return "<script>{$identifier} || document.write('<script type=\"text/javascript\" src=\"{$fallback}\"><\\/script>')</script>".PHP_EOL;
	}

	/**
	 * Get file name from local file and its path
	 * 
	 * @param  string  $file  File and its path
	 * 
	 * @return string
	 */
	protected function getName($file)
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
	protected function getVersion($file)
	{
		preg_match('/\d+(?:\.\d+)+/', $file, $matches);
		return $matches[0];
	}

	/**
	 * Get fallback identifier from file and its path
	 * 
	 * @param  string  $file  File and its path
	 * 
	 * @return string
	 * @throws \Exception
	 */
	protected function getIdentifier($file)
	{
		$identifier = $this->getRegistry(
			'identifier', $this->getLibraryName($file)
		);
		
		if (! is_null($identifier)) {
			return $identifier;
		} else {
			return $this->getRegistry(
				'identifier', $this->getName($file)
			);
		}

		throw new \Exception(
			"Could not find matches identifier from {$file}. ".
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
	protected function getExtension($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}

	/**
	 * Get CDN from \Registry class
	 * 
	 * @param  string  $cdn  CDN key
	 * 
	 * @return  string
	 */
	protected function getCdn($cdn = null)
	{
		if (! is_null($cdn) && ! is_null($this->getRegistry('cdn', $cdn))) {
			return $cdn;
		}

		return $this->getDefaultRegistry('cdn');
	}

	/**
	 * Check if current local file is minified
	 * 
	 * @param  string  $file  Current local file
	 * 
	 * @return string
	 */
	protected function isMinified($file)
	{
		$min = $this->minified;
		if (isset($min) && is_bool($min)) {
			return $min ? '.min' : '';
		}

		return stripos(basename($file), '.min') !== false ? '.min' : '';
	}

	/**
	 * Check whether file is a URL
	 * 
	 * @param  string  $cdn  File or path to check
	 * 
	 * @return bool
	 */
	public function isUrl($cdn)
	{
		 $regex = "([a-z0-9-.]*)\.([a-z]{2,3})";
		 $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";
		 
		 
		return preg_match("/$regex$/", $cdn);
	}

	/**
	 * Get library name from local file
	 * 
	 * @param  string  $file  Current local file
	 * 
	 * @return  string
	 */
	protected function getLibraryName($file)
	{
		return array_slice(explode('/', $file), -3, 1)[0];
	}

	/**
	 * Get data from \Registry class
	 * 
	 * @param  string  $registry  Registry key name
	 * @param  string  $list      Data key of value to retrieve
	 * 
	 * @return string
	 */
	private function getRegistry($registry, $list = null)
	{
		return $this->registry->{$registry}->get($list);
	}

	/**
	 * Perform data extraction to related assets
	 * 
	 * @param  string  $registry  Registry key name
	 * @param  array  $data       Data to match/extract to
	 * 
	 * @return array
	 */
	private function extractRegistry($registry, $data)
	{
		return $this->registry->{$registry}->extract($data);
	}

	/**
	 * Get default registry data
	 * 
	 * @param  string  $registry  Registry key name
	 * 
	 * @return  string
	 */
	private function getDefaultRegistry($registry)
	{
		return $this->registry->{$registry}->getDefault();
	}

	/**
	 * Dispatch registered \Registry classes
	 * 
	 * @return collection
	 */
	private function registerRegistry()
	{
		$registries = $this->registry;

		foreach ($registries as $registry) {
			$instance = new $registry($this);
			if (! $instance instanceof Registry) {
				throw new \Exception("Registry must be an instance of ".Registry::class.", {$registry} given");
			}
			$key = strtolower(substr(strrchr($registry, '\\'), 1));
			$newRegistry[$key] = $instance;
			unset($registries);
		}

		$this->registry = (object) $newRegistry;
	}



/////////////////CONTINUED


	public static function loads(array $files)
	{
		foreach ($files as $file) {
			$modifiedFile[] = [
				'name' => self::getName($file),
				'version' => self::getVersion($file),
				'extension' => self::getExtension($file),
				'cdn' => self::getcdn($file),
				'fallback' => self::getFallback($file),
			];
		}

		return (new Asbak)->render(new AssetsBuilder($modifiedFile));
	}

	protected function renders(AssetsBuilder $builder)
	{
		echo '<script>';
		echo self::internalScript();
		foreach ($builder->cdn() as $file) {
			echo $file;
		}

		foreach ($builder->fallback() as $script) {
			echo $script;
		}
		echo '</script>';
	}

	protected static function internalScripts()
	{
		$asbak = <<<EOF
			var Asbak = Asbak || (function(){
					var js = function(filename) {
						var tag = document.createElement('script');
		                tag.setAttribute('type', 'text/javascript');
		                tag.setAttribute('src', filename);

		                return tag;
					}

					var css = function(filename) {
						var tag = document.createElement('link');
						tag.setAttribute('rel', 'stylesheet');
						tag.setAttribute('type', 'text/css');
						tag.setAttribute('href', filename);

						return tag;
					}

					var load = function(filename) {
						var getExt = function(filename) {
							return filename.substr(filename.lastIndexOf('.')+1);
						}

						var Ext = getExt(filename);
						
						if (Ext === 'js' || Ext === 'css') {
							return ['Ext'](filename)
						}
						
					}

					var check = function(tag) {
						if(typeof tag != 'undefined') {
						    document.getElementsByTagName('head')[0].appendChild(tag);
						}
					}

					return {
						load: function(filename) {
							window.onload = load(filename);
						}
					}
				}());
EOF;
		return $asbak;
	}
}
