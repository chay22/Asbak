<?php 

namespace Chay22\Asbak\Repositories;

use Chay22\Asbak\Contracts\Repository;
use Chay22\Asbak\Asbak;

class Identifier implements Repository
{
	const _DEFAULT_ = 'google';

	private $lib = [
	'angular'			=>  'window.angular',
	'angular-material'	=>  'window.ngMaterial',
	'backbone'			=>	'window.backbone',
	'backbone.babysitter'=>	'window.Backbone.ChildViewContainer',
	'backbone.marionette'=>	'window.Backbone.Marionette',
	'backbone.radio'	=>	'window.Backbone.Radio',
	'backbone.syphon'	=>	'window.Backbone.Syphon',
	'backbone.wreqr'	=>	'window.Backbone.Wreqr',
	'backbone-forms'	=>	'window.Backbone.Form',
	'backbone.localstorage'=>'window.Backbone.LocalStorage',
	'backbone-relational'=>	'window.Backbone.Relational',
	'dojo'				=>  'window.dojo',
	'ember'				=>	'window.Ember',			'emberjs'		=>	'window.Ember',
	'ember-data'		=>	'window.DS.VERSION',
	'extcore'			=>	'window.Ext',			'ext-core'		=>	'window.Ext',
	'hammer'			=>  'window.Hammer',		'hammerjs'		=>	'window.Hammer',
	'jquery'			=>  'window.jQuery',
	'jquery-mobile'		=>	'window.jQuery.mobile',
	'jquery-ui	'		=>	'/\d+(?:\.\d+)+/.test(window.jQuery.ui.version)',
	'jqueryui'			=>	'/\d+(?:\.\d+)+/.test(window.jQuery.ui.version)',
	'knockout'			=>	'window.ko',
	'knockback'			=>	'window.kb.VERSION','knockback-core-stack'=>'window.kb.VERSION',
	'mootools'			=>	'window.MooTools',
	'nuclear'			=>	'window.Nuclear',		'nuclear-js'	=>	'window.Nuclear',
	'polymer'			=>	'window.PolymerGestures',
	'prototype'			=>	'window.Prototype.Version',
	'prototypejs'		=>	'window.Prototype.Version',
	'react'				=>	'window.React',			'reactjs'		=>	'window.React',
	'reactwithaddon'	=>	'window.React',
	'reactdom'			=>	'window.ReactDOM',
	'reactdomserver'	=>	'window.ReactDOMServer',
	'scriptaculous'		=>	'window.scriptaculous',
	'spf'				=>	'window.spf',			'spfjs'			=>	'window.spf',
	'swfobject'			=>	'window.swfobject',
	'three'				=>	'window.THREE',			'threejs'		=>	'window.THREE',
	'underscore'		=>	'window._',				'underscorejs'	=>	'window._',
	'vue'				=>	'window.Vue',			'vuejs'			=>	'window.Vue',
	'vuex'				=>	'window.Vuex',
	'vue-resource'		=>	'window.VueResource',
	'vue-router'		=>	'window.VueRouter',
	'vue-strap'			=>	'window.VueStrap',
	'vue-validator'		=>	'window.VueValidator',
	'webfont'			=>	'window.WebFont',		'webfontloader'	=>	'window.WebFont',

	];

	protected $asbak;

	public function __construct() {}

	public function get($key = null)
	{
		if (is_null($key)) {
			return $this->lib;
		}

		return isset($this->lib[$key]) ? $this->lib[$key] : null;
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