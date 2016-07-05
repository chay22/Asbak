<?php
/**
 * Generates assets global configuration.
 *
 * @package Asbak
 * @author Cahyadi Nugraha <cnu@protonmail.com>
 * @link https://github.com/chay22/Asbak Github page
 * @license http://github.com/chay22/Asbak/LICENSE MIT LICENSE
 */
namespace Chay22\Asbak;

class Config
{
    /**
     * Assets global configuration
     * @var array
     */
    private $config;

    /**
     * Begin initiation.
     * @param  array $config Assets global configuration
     * @param  array $data Assets data
     * @return void
     */
    public function __construct(array $config)
    {
        $this->config = $this->validateConfigData($config);
    }

    /**
     * Get configuration data
     * @return array Checked configuration data
     */
    public function data()
    {
        return $this->config;
    }

    /**
     * Checks given configuration data.
     * @param  array $config Given config data
     * @return array Checked configuration data
     */
    protected function validateConfigData(array $config)
    {
        //throws exception if all required key is null
        if (is_null($config['dir']) && is_null($config['js_dir']) && is_null($config['css_dir'])) {
            throw new \Chay22\Asbak\Exceptions\NotFoundException(
                "Couldn't find either 'dir', 'js_dir', or 'css_dir' on configuration."
            );
        }

        foreach (array_keys($config) as $key) {
            $conf[$key] = $config[$key];

            if (! is_null($config[$key])) {
                $method = Util::camelize($key) . 'Config';

                if (method_exists($this, $method)) {
                    $conf[$key] = $this->{$method}($config[$key]);
                }
            }
        }

        return $conf;
    }

    /**
     * Checks for given path existence and permission.
     * @param  string $path Assets local path directory
     * @return string Assets local path
     * @throws \Chay22\Asbak\Exceptions\AsbakException
     * @throws \Chay22\Asbak\Exceptions\NotFoundException
     */
    protected function dirConfig($path)
    {
        if (Util::isDir($path)) {
            if (is_writable($path)) {
                return $path;
            } else {
                throw new \Chay22\Asbak\Exceptions\AsbakException(
                    sprintf("Directory %s is not writable", $path)
                );
            }
        }

        throw new \Chay22\Asbak\Exceptions\NotFoundException(
            sprintf("Directory %s does not exists", $path)
        );
    }

    /**
     * Checks for given path existence and permission.
     * @param  string $path CSS path directory
     * @see Config::dirConfig()
     */
    protected function cssDirConfig($path)
    {
        return $this->dirConfig($path);
    }

    /**
     * Checks for given path existence and permission.
     * @param  string $path JS path directory
     * @see Config::dirConfig()
     */
    protected function jsDirConfig($path)
    {
        return $this->dirConfig($path);
    }

    /**
     * Checks for debug value type.
     * @param  int $level Assets debug level
     * @return int Assets debug level
     * @throws \Chay22\Asbak\Exceptions\WrongDataException
     */
    protected function debugConfig($level)
    {
        if (is_int($level)) {
            return $level;
        }

        throw new \Chay22\Asbak\Exceptions\WrongDataException(
            sprintf("Debug level needs to be integer, %s given", gettype($level))
        );
    }

    /**
     * Checks for cache value data type.
     * @param  bool $level Assets cache setting
     * @return bool Assets cache setting
     * @throws \Chay22\Asbak\Exceptions\WrongDataException
     */
    protected function cacheConfig($setting)
    {
        if (is_bool($setting)) {
            return $setting;
        }

        throw new \Chay22\Asbak\Exceptions\WrongDataException(
            sprintf("Cache setting needs to be boolean, %s given", gettype($setting))
        );
    }

    /**
     * Checks if write value is boolean type.
     * @param  bool $perform Perform file writting
     * @return bool
     * @throws \Chay22\Asbak\Exceptions\WrongDataException
     */
    protected function writeConfig($perform)
    {
        if (is_bool($perform)) {
            return $perform;
        }

        throw new \Chay22\Asbak\Exceptions\WrongDataException(
            sprintf("Write setting needs to be boolean, %s given", gettype($perform))
        );
    }
}
