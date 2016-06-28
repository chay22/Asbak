<?php 

namespace Chay22\Asbak\Repositories;

use Chay22\Asbak\Contracts\Repository;
use Chay22\Asbak\Asbak;

class CDN implements Repository
{
	const _DEFAULT_ = 'google';

	private $cdn = [
	'asp'			=>  'https://ajax.aspnetcdn.com/ajax/%library/%version/%name%min.%extension',
	'baidu'			=>  'http://libs.baidu.com/%name/%version/%name%min.%extension',
	'bootcdn'		=>  'https://cdn.bootcss.com/%name/%version/%name%min.%extension',
	'bootstrap'		=>  'https://maxcdn.bootstrapcdn.com/bootstrap/%version/%extension/%name%min.%extension',
	'cdnjs'			=>  'https://cdnjs.cloudflare.com/ajax/libs/%library/%version/%name%min.%extension',
	'cloudflare'	=>  'https://cdnjs.cloudflare.com/ajax/libs/%library/%version/%name%min.%extension',
	'facebook'		=>	'https://fb.me/%name-%version%min.%extension',
	'google'		=>  'https://ajax.googleapis.com/ajax/libs/%library/%version/%name%min.%extension',
	'jquery'		=>	'https://code.jquery.com/%name-%version%min.js',
	'jsdelivr'		=>  'https://cdn.jsdelivr.net/%name/%version/%name%min.%extension',
	'npmcdn'		=>  'https://npmcdn.com/%name@%version/dist/%name%min.js',
	'sina'			=>  'https://lib.sinaapp.com/%extension/%library/%version/%name%min.%extension',
	'sinaapp'		=>  'https://lib.sinaapp.com/%extension/%library/%version/%name%min.%extension',
	'useso'			=>  'http://ajax.useso.com/ajax/libs/%library/%version/%name%min.%extension',
	'yandex'		=>  'https://yastatic.net/%name/%version/%name%min.%extension',
	];

	protected $asbak;

	public function __construct() {}

	public function get($key = null)
	{
		if (is_null($key)) {
			return $this->cdn;
		}

		return isset($this->cdn[$key]) ? $this->cdn[$key] : null;
	}

	public function extract(array $data)
	{
		$cdn = $this->get($data['cdn']);

		foreach($data as $key => $val){
		    $data['%'.$key] = $val;
		    unset($data[$key]);
		}

		return str_replace(array_keys($data), $data, $cdn);
	}

	public function getDefault()
	{
		return self::_DEFAULT_;
	}
}