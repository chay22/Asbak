# Asbak
Build assets from CDN and perform a fallback, automagically.

Though rolling up scripts is at its ease these days, laying under browser cache is somewhat a demand.
Yes! CDN may give us a power in order to load our sites faster, through client's browser cache. But that
doesn't simply fit our needs at all. CDN can sometimes failed to load, or even blocked by your country.
And that seems like a serious problems will happening there.

> :information_source: Asbak is a PHP library. It was made because of the idea given by my answer on [Stack Overflow](http://stackoverflow.com/a/37861271/5816907).

## Contents
* [Getting Started](#getting-started)
  * [Installation](#installation)
  * [Example](#example)
* [Usage](#usage)
  * [Methods](#methods)
  * [Parameters](#parameters)
  * ["Keys" Definition](#keys-definition)
* [License](#license)

---
## Getting Started
### Introduction
Asbak creates and renders assets from array containing data its needed. It will firstly build the given data into CDN URL, check for its
availibility, then wrap it to corresponding HTML tag. And finally, Asbak will store the assets from CDN to local storage folder, to perform
a final fallback. That's it! Asbak is actually checking for CDN assets availibility for twice — server-side and client-side, automagically!

### Installation
Asbak can only be installed by composer. Simply run this at your command line
```
composer require chay22/asbak
```
---

### Example
```
$config = Asbak::config([
    'dir' => __DIR__ . '/public/assets',
]);

Asbak::load($config, 
    ['filename' => 'public/assets/js/jquery.min.js', 'version' => '1.11.3'],
    ['filename' => 'public/assets/js/vue.min.js', 'version' => '1.0.25'],
);
```

**That's all!** It will ended up in HTML script similar like this

```
<script type="text/javascript src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.3/jquery.min.js?ver=577c2d4d4db35"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js?ver=577c2d66532d5"></script>

<script>
window.jQuery || document.write('<script type="text/javascript" src="public/assets/js/jquery.min.js?ver=577c2d6d1f839"><\/script>');
window.Vue || document.write('<script type="text/javascript" src="public/assets/js/vue.min.js?ver=577c2d6d1f839"><\/script>');
</script>
```

> :information_source: If you set data `filename` into valid CDN URL, Asbak will then perform the fallback into that URL, instead of local file.

---

## Usage
Asbak is a static class that has only 2 public methods that you can actually use. They are `config()` and `load()`. In order to load assets
with Asbak, you need firstly set the _global_ configuration data properly with `config()` method then pass it to one main Asbak method `load()`.

### Methods

---
#### `Asbak::config`
_Generates Asbak global configuration data._
<pre><code><em>array</em> <b>Asbak::config</b> ( <em>array</em> <b>$config</b> )</code></pre>

<pre>
<b>Parameters</b>
  - <em>config</em>
    An array containing key of assets configuration.
<b>Returns</b>
  An array that <code>Asbak::load</code> needs as its first argument.
</pre>

---
#### `Asbak::load`
<em>Asbak main method to load the assets.</em>
<pre><code><em>string</em> <b>Asbak::load</b> ( <em>Asbak::config</em> <b>$config</b>, <em>array</em> <b>$data</b> [, <em>array</em> <b>$...</b>] )</code></pre>

<pre><b>Parameters</b>
  - <em>config</em>
    An array containing key of assets configuration. The array must be loaded from <code><i>Asbak::config</i></code> method.
  - <em>data</em>
    Array containing key of assets meta data.

<b>Returns</b>
  Strings of HTML elements involving assets.
</pre>

> :exclamation: Since Asbak performs file writing, it's important to note that you may need to set the correct permission to the directory.

---
### Parameters
Most important thing about using Asbak, is to know its available parameters. Those are arrays with configurable keys. It's always good to define the value of all available
keys in order to get the expected results. But, as Asbak comes with such detection in order to load the "correct" assets, filling the value only on required keys is good
enough.

As Asbak comes with two [methods](#methods) that only accept arrays as its arguments, these are all acceptable keys that should been filled.

---
#### `config`
An array containing key to configure _global_ assets behaviour that used as the only argument for [`Asbak::config`](#asbakconfig) method. This mostly used for defining working directory in order to store the assets locally.

| Key        | Required  | Type      | Default |                               |
|:----------:|:---------:|:---------:|:-------:|:------------------------------|
| **dir**    | yes*      | _string_  | null    | Path to global directory      |
| **css_dir**| yes*      |_string_   | null    | Path to stylesheets directory |
| **js_dir** | yes*      |_string_   | null    | Path to JavaScript directory  |
| **write**  | no        |_boolean_  | true    | Allow file writing            |

<sup><b>*</b> At least one is defined.</sup>

> :exclamation: Value of all dir keys needs to be an **absolute path**

---
#### `data`
Array containing assets data. This array is used as [`Asbak::load`](#asbakload) method's second ~ n<sup>th</sup> arguments.

| Key            | Required  | Type      | Default |                                                     |
|:--------------:|:---------:|:---------:|:-------:|:----------------------------------------------------|
| **filename**   | yes       | _string_  | null    | A file name with its public relative path or an URL |
| **cdn**        | no        | _string_  | "cdnjs" | CDN name                                            |
| **library**    | no        | _string_  | null    | Name of assets library                              |
| **version**    | yes       | _string_  | null    | Assets version                                      |
| **type**       | no        | _string_  | null    | Type of assets, either js or css                    |
| **identifier** | no        | _string_  | null    | Way to identify whether assets is loaded or no      |

---
### "Keys" Definition

---
## License
[The MIT License](https://github.com/chay22/Asbak/blob/master/LICENSE)
