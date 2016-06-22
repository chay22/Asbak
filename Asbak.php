<?php

namespace Chay22\Asbak;

class Asbak
{

	protected static $cdn;
	protected static $fallback;

	public static function load(array $files)
	{
		if (array_keys($files) !== range(0, count($files) - 1)) {
			return self::build($files);
		}

		foreach ($files as $file) {
			$name = AssetsParser::getName($file);
			$version = AssetsParser::getVersion($file);
			$modifiedFile[]["{$name}:{$version"] = $file;
		}

		return self::build($modifiedFile);
	}

	protected static function build(array $files)
	{
		return self::render(new AssetsBuilder($files));
	}

	protected static function render(AssetsBuilder $builder)
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

	protected static function internalScript()
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