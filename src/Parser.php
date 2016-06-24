<?php

namespace Chay22\Asbak;

class Parser
{
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
		return preg_match('/\d+(?:\.\d+)+/', $file, $matches)[0];
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

	public function getHost($file)
	{
		return $this->host;
	}

	public function __callStatic($method, $args)
	{
		return call_user_func_array($method, $args);
	}
}




$asbak = new Asbak();
$asbak->js()


var_dump($asbak);
Asbak::config(['host' => 'google'])
     ->js('./asdasdas/jQuery/1.2.3/jQuery.min.js',['name'=>'jQuery','version'=>'1.2.3','host'=>'google']);
     
Asbak::load([
    'abcd/asdads/asdasd/asdas.js'
])
