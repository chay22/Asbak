<?php

namespace Chay22\Asbak;

usw Chay22\Asbak\CDN;

class Asbak
{

	protected $file;
	protected $name;
	protected $version;
	protected $extension;
	protected $cdn;

	protected $registry = [];

	protected static $cdn;
	protected static $fallback;

	public function __construct()
	{
		$this->registry(new CDN($this));
	}

	public function js($file)
	{
		$this->file = $file;
		return $this;
	}

	public function name($name)
	{
		$this->name = $name;
		return $this;
	}

	public function version($version)
	{
		$this->version = $version;
		return $this;
	}

	public function extension($extension)
	{
		$this->extension = $extension;
		return $this;
	}

	public function cdn($cdn = null)
	{
		$this->cdn = $cdn;

		if (is_null($cdn)) {
			$this->cdn = $this->registry;
		}

		return $this;
	}

	public function __toString()
	{
		$fallback = $this->file;

		$name = isset($this->name)
			  ? $this->name : $this->getName($fallback);
		
		$version = isset($this->version)
				 ? $this->version : $this->getVersion($fallback);
		
		$extension = isset($this->extension)
				   ? $this->extension : $this->getExtension($fallback);
		
		$cdn = isset($this->cdn)
			  ? $this->cdn : $this->getcdn($fallback);

		$data = [
		  	'name' => $name,
		  	'version' => $version,
		  	'extension' => $extension,
		  	'cdn' => $cdn,
		  	'fallback' => $fallback,
		];

		return $this->render($data);
	}

	public function render(array $data)
	{
		return $this->script($data) . $this->fallback($data);
	}

	protected function script($data)
	{
		$cdn = $data['cdn'] === 'google' ? 'https://ajax.googleapis.com/ajax/libs' : 'https://ajax.googleapis.com/ajax/libs';
		$url = $cdn . "/" . $data['name'] . "/" . $data['version'] . "/" . $data['name'] . '.min.' . $data['extension'];

		return '<script type="text/javascript" src="'.$url.'"></script>'.PHP_EOL;
	}

	protected function fallback($data)
	{
		$fallback = $data['fallback'];
		$identifier = stripos($data['name'], 'jquery') ? 'window.jQuery' : 'window';

		return "<script>{$identifier} || document.write('<script type=\"text/javascript\" src=\"{$fallback}\"><\\/script>')</sscript>".PHP_EOL;
	}

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

	public function getVersion($file)
	{
		preg_match('/\d+(?:\.\d+)+/', $file, $matches);
		return $matches[0];
	}

	public function getIdentifier($file)
	{
		$ext = self::getExtension($file);

		if ($ext != 'js' || $ext != 'css') {
			throw new \Exception("File extension can only be either .js or .css");
		}

		return new Assets . ucwords($ext);
	}

	public function getExtension($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}

	public function getcdn($file)
	{
		return $this->cdn;
	}

	public function registry(Registry $registry)
	{
		$this->registry += $registry;
	}








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
