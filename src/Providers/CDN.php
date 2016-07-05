<?php
/**
 * CDN URL collection.
 *
 * @package Asbak
 * @author Cahyadi Nugraha <cnu@protonmail.com>
 * @link https://github.com/chay22/Asbak Github page
 * @license http://github.com/chay22/Asbak/LICENSE MIT LICENSE
 */
namespace Chay22\Asbak\Providers;

use Chay22\Asbak\Providers\ProviderInterface as Provider;

class CDN implements Provider
{
    /**
     * Predefined CDN host and URL list
     * @var array
     */
    private $cdn = [
    'asp'           =>  'https://ajax.aspnetcdn.com/ajax/%library/%version/%name',
    'baidu'         =>  'http://libs.baidu.com/%name/%version/%name',
    'bootcdn'       =>  'https://cdn.bootcss.com/%name/%version/%name',
    'bootstrap'     =>  'https://maxcdn.bootstrapcdn.com/bootstrap/%version/%type/%name',
    'cdnjs'         =>  'https://cdnjs.cloudflare.com/ajax/libs/%library/%version/%name',
    'cloudflare'    =>  'https://cdnjs.cloudflare.com/ajax/libs/%library/%version/%name',
    'facebook'      =>  'https://fb.me/%name-%version',
    'google'        =>  'https://ajax.googleapis.com/ajax/libs/%library/%version/%name',
    'jquery'        =>  'https://code.jquery.com/%name-%version.js',
    'jsdelivr'      =>  'https://cdn.jsdelivr.net/%name/%version/%name',
    'npmcdn'        =>  'https://npmcdn.com/%name@%version/dist/%name.js',
    'sina'          =>  'https://lib.sinaapp.com/%type/%library/%version/%name',
    'sinaapp'       =>  'https://lib.sinaapp.com/%type/%library/%version/%name',
    'useso'         =>  'http://ajax.useso.com/ajax/libs/%library/%version/%name',
    'yandex'        =>  'https://yastatic.net/%name/%version/%name',
    ];

    /**
     * Get predefined CDN value
     * @param  null|string $key null|CDN key
     * @return string|array|null CDN URL
     */
    public function get($key = null)
    {
        if (is_null($key)) {
            return $this->cdn;
        }

        return isset($this->cdn[$key]) ? $this->cdn[$key] : null;
    }

    /**
     * Transform assets data to valid CDN URL
     * @param  array  $data Assets data
     * @return string       CDN URL
     */
    public function extract(array $data)
    {
        $cdn = $this->get($data['cdn']);

        foreach ($data as $key => $val) {
            $data['%'.$key] = $val;

            unset($data[$key]);
        }

        return str_replace(array_keys($data), $data, $cdn);
    }
}
