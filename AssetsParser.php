<?php

namespace Chay22\Asbak;

class AssetsParser
{
	public static function getName($file)
	{
		$filename = basename($file);
		$ext = self::getExtension($file);

		if (stripos($filename, '.min.')) {
			$name = explode('.min.', $filename);
		} else {
			$name = explode('.' . $ext, $filename);
		}

		return $name[0];
	}

	public static function getVersion($file)
	{
		return preg_match('/\d+(?:\.\d+)+/', $file, $matches)[0];
	}

	public static function getIdentifier($file)
	{
		$ext = self::getExtension($file);

		if ($ext != 'js' || $ext != 'css') {
			throw new \Exception("File extension can only be either .js or .css");
		}

		return new Assets . ucwords($ext);
	}

	public static function getExtension($file)
	{
		return pathinfo($file, PATHINFO_EXTENSION);
	}
}