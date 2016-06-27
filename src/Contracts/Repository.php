<?php 

namespace Chay22\Asbak\Contract;

use Chay22\Asbak\Asbak;

interface Repository
{

	public function __construct(Asbak $asbak);

	public function get($key);
}