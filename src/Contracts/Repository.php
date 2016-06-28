<?php 

namespace Chay22\Asbak\Contracts;

interface Repository
{

	public function __construct();

	public function get($key);
}