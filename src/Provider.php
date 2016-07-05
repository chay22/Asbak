<?php
/**
 * Transforms null data value into Asbak generated data.
 *
 * @package Asbak
 * @author Cahyadi Nugraha <cnu@protonmail.com>
 * @link https://github.com/chay22/Asbak Github page
 * @license http://github.com/chay22/Asbak/LICENSE MIT LICENSE
 */
namespace Chay22\Asbak;

use Chay22\Asbak\Providers\ProviderInterface;

class Provider
{
    /**
     * Default CDN
     * @var string
     */
    private $defaultCdn = 'cloudflare';

    /**
     * Bootstrap provider classes.
     * @var array
     */
    private $provider = [
        '\Chay22\Asbak\Providers\CDN',
        '\Chay22\Asbak\Providers\Identifier',
    ];

    /**
     * Begin initiation.
     * @param  array $config Assets global configuration
     * @param  array $data Assets data
     * @return void
     */
    public function __construct(array $config, array $data)
    {
        $this->registerProviders();

        $this->config = $config;

        $this->data = $this->alterData($data);
    }

    /**
     * Alter given data into solid Asbak assets data
     * @param  array  $data Assets data collection
     * @return array        Altered data
     */
    protected function alterData(array $data)
    {
        foreach ($data as $val) {
            $newData[] = $this->buildNullData($val);
        }

        return $newData;
    }

    /**
     * Replace every null data from existing data and providers
     * @param  array $data Assets data
     * @return array       Generated assets data
     */
    protected function buildNullData(array $data)
    {
        foreach (array_keys($data) as $key) {
            $method = 'get'. ucfirst($key);

            if (is_null($data[$key])) {
                if (method_exists($this, $method)) {
                    $data[$key] = $this->{$method}($data);
                }
            }
        }

        return $data;
    }

    /**
     * Get assets data
     * @return array Assets data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set file name as same as library name if null.
     * @param array $data Assets data
     * @return void
     * @throws \Chay22\Asbak\Excepions\NotFoundException
     */
    protected function getFilename()
    {
        throw new \Chay22\Asbak\Exceptions\NotFoundException(
            "Data 'filename' not found."
        );
    }

    /**
     * Get file name without path from data filename
     * @param  array   $data Assets data
     * @return string        Base name of file
     */
    protected function getName(array $data)
    {
        if (! is_null($data['filename'])) {
            return basename($data['filename']);
        }

        throw new \Chay22\Asbak\Exceptions\NotFoundException(
            "Data 'filename' not found."
        );
    }

    /**
     * Set library name from given file name if null.
     * @param  array $data Assets data
     * @return string Library name
     * @throws \Chay22\Asbak\Exceptions\NotFoundException
     */
    protected function getLibrary(array $data)
    {
        if (is_null($filename = basename($data['filename']))) {
            throw new \Chay22\Asbak\Exceptions\NotFoundException(
                "Data name not found. Please set either data library or file name or both."
            );
        }

        $ext = $this->getType($filename);

        $name = explode('.' . $ext, $filename);

        if (stripos($filename, '.min.')) {
            $name = explode('.min.', $filename);
        } elseif (stripos($filename, '-min.')) {
            $name = explode('-min.', $filename);
        }

        return $name[0];
    }

    /**
     * Throw an error if data version is not set.
     * @param  string $data Assets data
     * @return void
     * @throws \Chay22\Asbak\Exceptions\WrongDataException
     */
    protected function getVersion(array $data)
    {
        throw new \Chay22\Asbak\Exceptions\WrongDataException(
            sprintf("Version cannot be %s. Please set it first.", $data['version'])
        );
    }

    /**
     * Set identifier from available data library, if data library
     * doesn't exists it will be defined from data name and if data
     * name doesn't exists, error will thrown.
     * @param  array $data Assets data
     * @return string Assets fallback identifier
     * @throws \Chay22\Asbak\Exceptions\NotFoundException;
     */
    protected function getIdentifier($data)
    {
        if (is_null($data['library']) && is_null($data['name'])) {
            throw new \Chay22\Asbak\Exceptions\NotFoundException(
                "Data name and library not found. Please set either data library or file name or both."
            );
        } elseif (is_null($data['library']) && ! is_null($data['name'])) {
            $library = $this->getLibrary($data);
        } else {
            $library = $data['library'];
        }

        if (! is_null($identifier = $this->provider['identifier']->get($library))) {
            return $identifier;
        }

        throw new \Chay22\Asbak\Exceptions\NotFoundException(
            "Could not find any matches identifier. Please set it first."
        );
    }

    /**
     * Get file type from data name
     * @param  array $data Assets data
     * @return string File type
     * @throws \Chay22\Asbak\Exceptions\NotFoundException
     */
    protected function getType($data)
    {
        if (! is_null($data['filename'])) {
            return pathinfo($data['filename'], PATHINFO_EXTENSION);
        }

        throw new \Chay22\Asbak\Exceptions\NotFoundException(
            "Data filename not found."
        );
    }

    /**
     * If CDN is not set, get it from default CDN if it set,
     * check it first from provider. Return it if exists, otherwise
     * fallback to default CDN.
     * @param  array $data Assets data
     * @return string Data CDN
     */
    protected function getCdn($data)
    {
        $cdn = $data['cdn'];

        $repoCdn = $this->provider['cdn']->get($cdn);

        if (! is_null($cdn) && ! is_null($repoCdn)) {
            return strtolower($cdn);
        }

        return $this->defaultCdn;
    }

    /**
     * Dispatch registered \Provider classes
     * @return collection
     * @throws \Chay22\Asbak\Exceptions\WrongDataException;
     */
    protected function registerProviders()
    {
        $providers = $this->provider;

        foreach ($providers as $provider) {
            $instance = new $provider();
            if (! $instance instanceof ProviderInterface) {
                throw new \Chay22\Asbak\Exceptions\WrongDataException(
                    "Class must be an instance of " .
                    "\Chay22\Asbak\Providers\ProviderInterface interface class," .
                    "{$provider} given."
                );
            }

            $key = strtolower(substr(strrchr($provider, '\\'), 1));

            $newProvider[$key] = $instance;

            unset($providers);
        }

        $this->provider = $newProvider;
    }

    /**
     * Returns instace of \Chay22\Asbak\Providers\ProviderInterface class
     * @param  string $name Provider class name
     * @return class \Chay22\Asbak\Providers\ProviderInterface
     */
    public function provider($name)
    {
        return $this->provider[$name];
    }
}
