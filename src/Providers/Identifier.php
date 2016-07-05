<?php
/**
 * Assets identifier collection.
 *
 * @package Asbak
 * @author Cahyadi Nugraha <cnu@protonmail.com>
 * @link https://github.com/chay22/Asbak Github page
 * @license http://github.com/chay22/Asbak/LICENSE MIT LICENSE
 */
namespace Chay22\Asbak\Providers;

use Chay22\Asbak\Providers\ProviderInterface as Provider;

class Identifier implements Provider
{
    /**
     * Predefined library name and its identifier list
     * @var array
     */
    private $lib = [
    'angular'           =>  'window.angular',
    'angular-material'  =>  'window.ngMaterial',
    'backbone'          =>  'window.backbone',
    'backbone.babysitter'=> 'window.Backbone.ChildViewContainer',
    'backbone.marionette'=> 'window.Backbone.Marionette',
    'backbone.radio'    =>  'window.Backbone.Radio',
    'backbone.syphon'   =>  'window.Backbone.Syphon',
    'backbone.wreqr'    =>  'window.Backbone.Wreqr',
    'backbone-forms'    =>  'window.Backbone.Form',
    'backbone.localstorage'=>'window.Backbone.LocalStorage',
    'backbone-relational'=> 'window.Backbone.Relational',
    'dojo'              =>  'window.dojo',
    'ember'             =>  'window.Ember',         'emberjs'       =>  'window.Ember',
    'ember-data'        =>  'window.DS.VERSION',
    'extcore'           =>  'window.Ext',           'ext-core'      =>  'window.Ext',
    'hammer'            =>  'window.Hammer',        'hammerjs'      =>  'window.Hammer',
    'jquery'            =>  'window.jQuery',
    'jquery-mobile'     =>  'window.jQuery.mobile',
    'jquery-ui  '       =>  '/\d+(?:\.\d+)+/.test(window.jQuery.ui.version)',
    'jqueryui'          =>  '/\d+(?:\.\d+)+/.test(window.jQuery.ui.version)',
    'knockout'          =>  'window.ko',
    'knockback'         =>  'window.kb.VERSION','knockback-core-stack'=>'window.kb.VERSION',
    'mootools'          =>  'window.MooTools',
    'nuclear'           =>  'window.Nuclear',       'nuclear-js'    =>  'window.Nuclear',
    'polymer'           =>  'window.PolymerGestures',
    'prototype'         =>  'window.Prototype.Version',
    'prototypejs'       =>  'window.Prototype.Version',
    'react'             =>  'window.React',         'reactjs'       =>  'window.React',
    'reactwithaddon'    =>  'window.React',
    'reactdom'          =>  'window.ReactDOM',
    'reactdomserver'    =>  'window.ReactDOMServer',
    'scriptaculous'     =>  'window.scriptaculous',
    'spf'               =>  'window.spf',           'spfjs'         =>  'window.spf',
    'swfobject'         =>  'window.swfobject',
    'three'             =>  'window.THREE',         'threejs'       =>  'window.THREE',
    'underscore'        =>  'window._',             'underscorejs'  =>  'window._',
    'vue'               =>  'window.Vue',           'vuejs'         =>  'window.Vue',
    'vuex'              =>  'window.Vuex',
    'vue-resource'      =>  'window.VueResource',
    'vue-router'        =>  'window.VueRouter',
    'vue-strap'         =>  'window.VueStrap',
    'vue-validator'     =>  'window.VueValidator',
    'webfont'           =>  'window.WebFont',       'webfontloader' =>  'window.WebFont',

    ];

    /**
     * Get predefined identifier value from library name
     * @param  null|string $key null|Library name
     * @return mixed            Assets identifier
     */
    public function get($key = null)
    {
        if (is_null($key)) {
            return $this->lib;
        }

        return isset($this->lib[$key]) ? $this->lib[$key] : null;
    }
}
