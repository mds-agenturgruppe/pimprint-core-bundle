<?php
/**
 * mds PimPrint
 *
 * This source file is licensed under GNU General Public License version 3 (GPLv3).
 *
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license    https://pimprint.mds.eu/license GPLv3
 */

namespace Mds\PimPrint\CoreBundle\Project;

use Pimcore\Tool;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Config
 *
 * @package Mds\PimPrint\CoreBundle\Project
 */
class Config implements \ArrayAccess
{
    /**
     * Configuration array.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * Lazy loading property.
     *
     * @var string
     */
    protected string $hostUrl;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->set($config);
    }

    /**
     * Sets $config array.
     *
     * @param array $config
     */
    public function set(array $config)
    {
        $this->config = $config;
    }

    /**
     * Sets $key with $value in config.
     * Method exists to add configuration parameters programmatically in projects.
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet(mixed $offset, mixed $value)
    {
        $this->config[$offset] = $value;
    }

    /**
     * Returns offset
     *
     * @param mixed $offset
     * @param mixed $default
     * @param bool  $required
     *
     * @return mixed
     * @throws \Exception
     */
    public function offsetGet(mixed $offset, mixed $default = '', bool $required = false): mixed
    {
        if (true === $this->offsetExists($offset)) {
            return $this->config[$offset];
        }
        if ($required) {
            $message =
                "Config offset '%s' not defined in PimPrint configuration or added programmatically for project '%s'.";
            throw new \Exception(sprintf($message, $offset, $this->config['ident']));
        }

        return $default;
    }

    /**
     * Checks if offset exists
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->config);
    }

    /**
     * Unsets offset
     *
     * @param mixed $offset
     */
    public function offsetUnset(mixed $offset)
    {
        unset($this->config[$offset]);
    }

    /**
     * Returns host url from project configuration or dynamically from $request.
     *
     * @param Request $request
     *
     * @return string
     * @throws \Exception
     */
    public function getHostUrl(Request $request): string
    {
        if (!empty($this->hostUrl)) {
            return $this->hostUrl;
        }
        try {
            $hostConfig = $this->offsetGet('host', '', true);

            $port = '';
            $protocol = $request->getScheme();
            $hostname = $request->getHost();

            if (!in_array($request->getPort(), [443, 80])) {
                $port = $request->getPort();
            }
            foreach (['hostname', 'protocol', 'port'] as $key) {
                if (false === array_key_exists($key, $hostConfig)) {
                    continue;
                }
                $$key = (string)$hostConfig[$key];
            }
            if (false === empty($port)) {
                $port = ':' . $port;
            }
            if (empty($hostname) || empty($protocol)) {
                throw new \Exception();
            }
            $url = $protocol . '://' . $hostname . $port;
        } catch (\Exception) {
            $url = Tool::getHostUrl(null, $request);
        }
        if (empty($url)) {
            throw new \Exception(
                sprintf("No host url found by Pimcore or configured for project '%s'", $this->config['ident'])
            );
        }
        $this->hostUrl = $url;

        return $this->hostUrl;
    }

    /**
     * Returns true if download for assets is enabled for current project.
     *
     * @return bool
     * @throws \Exception
     */
    public function isAssetDownloadEnabled(): bool
    {
        return $this->offsetGet('assets')['download'];
    }

    /**
     * Returns true if preDownload for assets is enabled for current project.
     *
     * @return bool
     * @throws \Exception
     */
    public function isAssetPreDownloadEnabled(): bool
    {
        if (false === $this->isAssetDownloadEnabled()) {
            return false;
        }

        return $this->offsetGet('assets')['pre_download'];
    }

    /**
     * Returns true if warnings for assets in onPage for current project.
     *
     * @return bool
     * @throws \Exception
     */
    public function isAssetWarningOnPage(): bool
    {
        return $this->offsetGet('assets')['warnings_on_page'];
    }
}
