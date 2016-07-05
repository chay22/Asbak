<?php
/**
 * Asbak factory class.
 *
 * @package Asbak
 * @author Cahyadi Nugraha <cnu@protonmail.com>
 * @link https://github.com/chay22/Asbak Github page
 * @license http://github.com/chay22/Asbak/LICENSE MIT LICENSE
 */
namespace Chay22\Asbak;

class Asbak
{
    /**
     * Avaliable data to build the assets
     * @var array
     */
    private static $data = [
        'filename', 'library', 'type', 'name',
        'version','cdn', 'identifier', 'fallback',
    ];

    private static $config = [
        'dir', 'css_dir', 'js_dir',
        'write', 'debug', 'cache',
    ];

    /**
     * Load data to build to assets
     * @return void
     */
    public static function load()
    {
        $count = func_num_args();

        $datas = func_get_args();

        $config = func_get_arg(0);

        for ($i = 0; $i < $count; $i++) {
            if ($i == 0) {
                continue;
            }

            foreach (array_keys($datas[$i]) as $key) {
                if (! in_array(strtolower($key), self::$data)) {
                    unset($datas[$i][$key]);
                }

                $data[$i] = self::arrayMerge(self::$data, $datas[$i]);
            }
        }

        $provider = new Builder($config, $data);

        echo $provider->cdnScript();

        echo $provider->fallbackScript();
    }

    /**
     * Set assets global configuration
     * @param  array  $config Config value
     * @return void
     */
    public static function config(array $config)
    {
        foreach (array_keys($config) as $key) {
            if (! in_array(strtolower($key), self::$config)) {
                unset($config[$key]);
            }

            $config = self::arrayMerge(self::$config, $config);

            //Override default config write value
            if (is_null($config['write'])) {
                $config['write'] = true;
            }
        }

        return $config;
    }

    /**
     * Merge given array to default given property and set null to it.
     * This ensure every requested array (data or config) has all key
     * from default data.
     * @param  array  $self  Array from property
     * @param  array  $array Requested array
     * @return array         Merged array of requested data with default data
     */
    private static function arrayMerge(array $self, array $array)
    {
        foreach ($self as $val) {
            $s[$val] = null;
        }

        return array_merge($s, $array);
    }
}
