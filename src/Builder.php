<?php
/**
 * Builds (post-generated) data plus global configuration data into HTML ready assets.
 *
 * @package Asbak
 * @author Cahyadi Nugraha <cnu@protonmail.com>
 * @link https://github.com/chay22/Asbak Github page
 * @license http://github.com/chay22/Asbak/LICENSE MIT LICENSE
 */
namespace Chay22\Asbak;

class Builder
{
    /**
     * Assets global configuration
     * @var array
     */
    protected $config;

    /**
     * Provider class
     * @var class \Chay22\Asbak\Provider
     */
    protected $provider;

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
        $conf = new Config($config);

        $this->config  = $conf->data();

        $this->provider = new Provider($this->config, $data);

        $this->data = $this->provider->getData();
    }

    /**
     * Generate HTML script tag containing CDN URL from data
     * @return string CDN script tags
     */
    public function cdnScript()
    {
        $file = new StoreToLocal($this->config, $this->data);

        $script = '';
        foreach ($this->data as $key => $data) {
            $url = $this->provider('cdn')->extract($data);

            if (! $file->read($url)) {
                $this->data[$key]['fallback'] = $url = $data['filename'];
            }

            $script .= sprintf('<script type="text/javascript" src="%s?ver=%s"></script>%s', $url, uniqid(), PHP_EOL);

            $local = $file->directory($data) . '/' . $data['name'];

            if ($this->config['write']) {
                $file->write($local, $url);

                $this->data[$key]['fallback'] = $local;
            }
        }

        return $script;
    }

    /**
     * Generate HTML script tag containing fallback from CDN
     * to local file
     * @return string Fallback scripts
     */
    public function fallbackScript()
    {
        $script = '<script>';

        foreach ($this->data as $data) {
            $identifier = $data['identifier'];

            $script .= sprintf("%s || document.write('<script type=\"text/javascript\" src=\"%s?ver=%s\"><\\/script>');%s", $identifier, $data['filename'], uniqid(), PHP_EOL);
        }

        $script .= '</script>';

        return $script;
    }

    /**
     * Returns instance of \Chay22\Asbak\Providers\ProviderInterface instance
     * @param  string $name Provider class name
     * @return class \Chay22\Asbak\Providers\ProviderInterface
     */
    protected function provider($name)
    {
        return $this->provider->provider($name);
    }
}
