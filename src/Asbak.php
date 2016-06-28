<?php

namespace Chay22\Asbak;

use Chay22\Asbak\Repositories\CDN;
use Chay22\Asbak\Repositories\Identifier;
use Chay22\Asbak\Contract\Repository;
use Chay22\Asbak\Services\AssetsParser as Parser;
use Chay22\Asbak\Services\RepositoryRegistry as Registry;

class Asbak
{
	/**
	 * Local assets file path.
	 * 
	 * @var string
	 */
	protected $file;

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
	 * Set debug parameter
	 * 
	 * @var int
	 */
	protected $debug;

	/**
	 * Bootstrap repositoy classes.
	 * 
	 * @var array
	 */
	protected $repo = [
		CDN::class,
		Identifier::class,
	];

	protected $parse;
	protected $registry;

	/**
	 * Begin initiation.
	 * 
	 * @return void
	 */
	public function __construct(array $config = null)
	{
		$this->repository = new Registry();

		$this->parse = new Parser($this);

		$this->debug = isset($config['debug'])
					 ? $config['debug'] + 0 : 0;
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

		$this->parse->js($file);

		return $this;
	}

	/**
	 * Set exact file name that going to be turned into CDN.
	 * 
	 * @param  string  $name  File name
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 */
	public function name($filename)
	{
		$this->parse->name($filename);

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
		$this->parse->library($name);

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
	public function version($file)
	{
		$this->parse->version($file);

		return $this;
	}

	/**
	 * Set file extension
	 * 
	 * @param  string  $extension  js|css  
	 * 
	 * @return class \Chay22\Asbak\Asbak
	 */
	public function extension($file)
	{
		$this->parse->extension($file);

		return $this;
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
	public function cdn($file)
	{
		$this->isUrl($file)
			  ? $this->parse->cdn($file)
			  : $this->repository->getCdn($file);

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
	public function min($minify = true)
	{
		$this->parse->min($minify);

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
	 * Re-validate all null data value
	 * 
	 * @return array
	 */
	public function data()
	{
		$file = $this->file;
		$data = $this->parse->get();

		foreach ($data as $key => $val) {
			if (is_null($data[$key])) {
				if (method_exists($this->repository, 'get'.ucfirst($key))) {
					$data[$key] = $this->repository
									   ->{'get'.ucfirst($key)}($file);
				}
			}
		}

		$data['min'] = $this->isMinified($data['min']);
		$data['fallback'] = $file;

		return $data;
	}

	/**
	 * Wrap existing data to HTML
	 * 
	 * @return string
	 */
	protected function wrap()
	{
		$data = $this->data();

		if ($this->debug == 3) {
			echo '<pre style="background-color:#1d1d1d !important;' .
				  'color:#fafafa !important;padding:2em 1em !important;' .
			 	  'border: .4em solid #212121 !important;margin: 0 -1em !important;'.
			 	  'font-size: .95em !important;position:absolute !important;'.
			 	  'width:100% !important;top:0 !important;">';
			var_export($data);
			echo '</pre>';
		}

		return $this->script($data) .
			   $this->fallback($data);
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
		$url = $this->repository('cdn')->get($data['cdn']);
		
		if (! is_null($url)) {
			$url = $this->repository('cdn')->extract($data);
		} else {
			$default = $this->repository('cdn')->getDefault();
			$url = $this->repository('cdn')->get($default);
			$url = $this->repository('cdn')->extract($data);
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
		$library = $data['library'];

		if ($this->debug == 1) {
			return "<script>if ({$identifier}) { console.log('{$library} loaded successfully from CDN') } ".
				   "else { console.log('{$library} is NOT loaded from CDN!'); ".
				   "document.write('<script type=\"text/javascript\" src=\"{$fallback}\"><\\/script>'); " . 
				   "document.write('<script>if ({$identifier}) { console.log(\'{$library} has loaded now\') } ".
				   "else {console.log(\'Error: {$library} has failed to load!\'); }<\/script> ');}</script>";

		}

		return "<script>{$identifier} || document.write('<script type=\"text/javascript\" src=\"{$fallback}\"><\\/script>')</script>".PHP_EOL;
	}


	/**
	 * Check if current local file is minified
	 * 
	 * @param  string  $file  Current local file
	 * 
	 * @return string
	 */
	public function isMinified($data)
	{
		$min = $data['min'];
		$file = basename($this->file);
		if (is_bool($min)) {
			return $min ? '.min' : '';
		}

		$pos = stripos($file, 'min');
		if ($pos !== false) {
			$min = substr($file, 0, $pos);
			$sep = substr($min, -1, 1);
			if ($sep === '.' || $sep === '-') {
				return "{$sep}min";
			}		
		}
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
	 * Return specific repository instances
	 *
	 * @param  string  $repo 	Repository key name
	 *
	 * @return classes implement \Chay22\Asbak\Contracts\Repository
	 */
	protected function repository($repo)
	{
		return $this->repository->{$repo};
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
		$this->repository('cdn')->setDefaultCdn($value);
	}
/////////////////CONTINUED


	public static function load(array $files, $config = null)
	{
		$assets = new Asbak($config);

		foreach ($files as $file) {
			if (pathinfo($file, PATHINFO_EXTENSION) == 'js') {
				$assets->js($file)->get();
			}
		}
	}

	protected function renderAsync($builder)
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

	protected static function asyncScript()
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
