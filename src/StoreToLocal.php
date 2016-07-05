<?php
/**
 * File reading (from CDN URL) and storing to local path.
 *
 * @package Asbak
 * @author Cahyadi Nugraha <cnu@protonmail.com>
 * @link https://github.com/chay22/Asbak Github page
 * @license http://github.com/chay22/Asbak/LICENSE MIT LICENSE
 */
namespace Chay22\Asbak;

class StoreToLocal
{
    /**
     * Assets global configuration
     * @var array
     */
    protected $config;

    /**
     * Assets data
     * @var array
     */
    protected $data;

    /**
     * Begin initiation.
     * @param  array $config Assets global configuration
     * @param  array $data Assets data
     * @return void
     */
    public function __construct(array $config, array $data)
    {
        $this->isAllowUrlFopenEnabled();

        $this->config = $config;

        $this->data = $data;
    }

    /**
     * Store assets file from CDN URL.
     * @param  string $path     Path to local file
     * @param  string $filename CDN URL
     * @return void
     * @throws \Chay22\Asbak\Exceptions\AsbakException
     */
    public function write($path, $filename)
    {
        if (! file_put_contents($path, $this->read($filename))) {
            throw new \Chay22\Asbak\Exceptions\AsbakException(
                sprintf("Failed to store assets from %s", $filename)
            );
        }

        clearstatcache();
    }

    /**
     * Read file from filename
     * @param  string $filename Name of file to read
     * @return mixed
     */
    public function read($filename)
    {
        $options = stream_context_create(['http' => [
            'ignore_errors' => true,
            'timeout' => 10,
        ]]);

        if (Util::onIis()) {
            $file = @file_get_contents($filename, false, $options);
        } else {
            $file = file_get_contents($filename, false, $options);
        }

        if (Util::parseStatusCode($http_response_header[0]) == 200) {
            return $file;
        }

        return false;
    }

    /**
     * Checks for current running server has allow_url_fopen enabled
     * @return void
     * @throws \Chay22\Asbak\Exceptions\AsbakException
     */
    public function isAllowUrlFopenEnabled()
    {
        if (! ini_get('allow_url_fopen')) {
            throw new \Chay22\Asbak\Exceptions\AsbakException(
                "Asbak needs setting of allow_url_fopen to be enabled."
            );
        }
    }

    /**
     * Get requested assets local path directory.
     * @param  array Data of requested assets
     * @return string Requested assets path
     * @throws \Chay22\Asbak\Exceptions\AsbakException
     * @throws \Chay22\Asbak\Exceptions\NotFoundException
     */
    public function directory($data)
    {
        $dir = $this->config['dir'];

        $jsDir = $this->config['js_dir'];

        $cssDir = $this->config['css_dir'];

        $type = $data['type'];

        if ($type == 'js' && isset($jsDir)) {
            return $jsDir;
        }

        if ($type == 'css' && isset($cssDir)) {
            return $cssDir;
        }

        $dir .= '/' . $type;

        if (Util::isDir($dir)) {
            if (is_writable($dir)) {
                return $dir;
            } else {
                throw new \Chay22\Asbak\Exceptions\AsbakException(
                    sprintf("Directory %s is not writable", $dir)
                );
            }
        }

        throw new \Chay22\Asbak\Exceptions\NotFoundException(
            sprintf("Directory %s does not exists", $dir)
        );
    }
}
