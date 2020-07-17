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
    protected $config = [];

    /**
     * Lazy loading property.
     *
     * @var string
     */
    protected $hostUrl;

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
    public function offsetSet($offset, $value)
    {
        $this->config[$offset] = $value;
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $offset
     * @param string $default
     * @param bool $required
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function offsetGet($offset, $default = '', $required = false)
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
     * {@inheritDoc}
     *
     * @param mixed $offset
     *
     * @return bool|void
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    /**
     * {@inheritDoc}
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->config[$offset]);
    }

    /**
     * Returns optional configured host url.
     *
     * @param Request $request
     *
     * @return string
     * @throws \Exception
     */

    /**
     * Returns host url from project configuration or dynamically from $request.
     *
     * @param Request $request
     *
     * @return string
     * @throws \Exception
     */
    public function getHostUrl(Request $request)
    {
        if (null !== $this->hostUrl) {
            return $this->hostUrl;
        }
        try {
            $hostConfig = $this->offsetGet('host', null, true);
            $request = Tool::resolveRequest($request);

            $protocol = 'http';
            $hostname = '';
            $port = '';
            if (null !== $request) {
                $protocol = $request->getScheme();
                $hostname = $request->getHost();

                if (!in_array($request->getPort(), [443, 80])) {
                    $port = $request->getPort();
                }
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
        } catch (\Exception $e) {
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
    public function isAssetDownloadEnabled()
    {
        return $this->offsetGet('assets')['download'];
    }

    /**
     * Returns true if preDownload for assets is enabled for current project.
     *
     * @return bool
     * @throws \Exception
     */
    public function isAssetPreDownloadEnabled()
    {
        if (false === $this->isAssetDownloadEnabled()) {
            return false;
        }

        return $this->offsetGet('assets')['preDownload'];
    }

    /**
     * Returns true if warnings for assets in onPage for current project.
     *
     * @return true
     * @throws \Exception
     */
    public function isAssetWarningOnPage()
    {
        return $this->offsetGet('assets')['warningsOnPage'];
    }
}
