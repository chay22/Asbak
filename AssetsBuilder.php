<?php 

namespace Chay22\Asbak;

class AssetsBuilder
{
	protected $name;
	protected $version;
	protected $host;
	protected $extension;
	protected $fallback;

	public function __construct($files)
	{
		foreach ($files as $keys => $value) {
			$key = explode(':', $keys);
			$name[] = $key[0];
			$version[] = end($key);
			$fallback[] = $value;
		}

		$this->name = $name;
		$this->version = $version;
		$this->fallback = $fallback;
	}

	public function cdn()
	{
		foreach ($this->name as $name) {
			foreach ($this->version as $version) {
				$cdn[] = "Asbak.load(//{$this->host}/{$version}/{$name}.min.{$this->extension})";
			}
		}

		return $cdn;
	}

	public function fallback()
	{
		$identifier = $this->identifier();
		foreach ($this->fallback as $fallback) {
			$script[] = "window.{$identifier} || document.write('<script type=\"text/javascript\" src=\"{$fallback}\"><\\/script>')";
		}

		return $script;
	}

	public function identifier();
}