<?php 

namespace Chay22\Asbak;

use Chay22\Asbak\Contract\Registry;

class CDN implements Registry
{
	private $cdn = [
	'asp'			=>  'https://ajax.aspnetcdn.com/ajax/%name/%version/%name%min.%extension',
	'baidu'			=>  'http://libs.baidu.com/%name/%version/%name%min.%extension',
	'bootcdn'		=>  'https://cdn.bootcss.com/%name/%version/%name%min.%extension',
	'bootstrap'		=>  'https://maxcdn.bootstrapcdn.com/bootstrap/%version/%extension/%name%min.%extension',
	'cdnjs'			=>  'https://cdnjs.cloudflare.com/ajax/libs/%name/%version/%name%min.%extension',
	'cloudflare'	=>  'https://cdnjs.cloudflare.com/ajax/libs/%name/%version/%name%min.%extension',
	'google'		=>  'https://ajax.googleapis.com/ajax/libs/%name/%version/%name%min.%extension',
	'jquery'		=>	'https://code.jquery.com/%name-%version%min.js',
	'jsdelivr'		=>  'https://cdn.jsdelivr.net/%name/%version/%name%min.%extension',
	'npmcdn'		=>  'https://npmcdn.com/%name@%version/dist/%name%min.js',
	'sina'			=>  'https://lib.sinaapp.com/%extension/%name/%version/%name%min.%extension',
	'sinaapp'		=>  'https://lib.sinaapp.com/%extension/%name/%version/%name%min.%extension',
	'useso'			=>  'http://ajax.useso.com/ajax/libs/%name/%version/%name%min.%extension',
	'yandex'		=>  'https://yastatic.net/%name/%version/%name%min.%extension',
	];

	protected $asbak;

	public function __construct(Asbak $asbak)
	{
		$this->asbak = $asbak;
	}

	public function get($key)
	{
		return $this->cdn['$key'];
	}
}