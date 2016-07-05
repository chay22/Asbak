<?php
/**
 * Helper class.
 *
 * @package Asbak
 * @author Cahyadi Nugraha <cnu@protonmail.com>
 * @link https://github.com/chay22/Asbak Github page
 * @license http://github.com/chay22/Asbak/LICENSE MIT LICENSE
 */
namespace Chay22\Asbak;

use Doctrine\Common\Inflector\Inflector;

class Util
{
    /**
     * Checks if this script running on IIS web server.
     * @return bool
     */
    public static function onIis()
    {
        $server = strtolower($_SERVER['SERVER_SOFTWARE']);

        if (strpos($server, "microsoft-iis") !== false) {
            return true;
        }

        return false;
    }

    /**
     * Converts string from 'table_name' to 'tableName' format.
     * @see \Doctrine\Common\Inflector\Inflector::camelize()
     * @param string $word The word to camelize.
     * @return string The camelized word.
     */
    public static function camelize($word)
    {
        return Inflector::camelize($word);
    }

    /**
     * Checks directory is exists and writable.
     * @param  string $path Directory path
     * @return bool
     */
    public static function isDir($path)
    {
        if (file_exists($path)) {
            if (is_dir($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns status code from header string, e.g "HTTP/1.1 200 OK"
     * @param  string $header Response header with version and status code
     * @return int            Status code
     */
    public static function parseStatusCode($header)
    {
        preg_match('/HTTP\\/[0-9\.]+\s+([0-9]+)/', $header, $match);

        return intval($match[1]);
    }
}
