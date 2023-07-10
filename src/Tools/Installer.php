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
 *
 * @phpcs:disable Generic.Files.LineLength.TooLong
 */

namespace Mds\PimPrint\CoreBundle\Tools;

use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;
use Pimcore\Extension\Bundle\Installer\InstallerInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Installer
 *
 * @package Mds\PimPrint\CoreBundle\Tools
 */
class Installer extends AbstractInstaller implements InstallerInterface
{
    /**
     * Bundle
     *
     * @var BundleInterface
     */
    private BundleInterface $bundle;

    /**
     * Runtime cache
     *
     * @var array
     */
    private array $securityConfig;

    /**
     * Installer constructor
     *
     * @param BundleInterface $bundle
     */
    public function __construct(BundleInterface $bundle)
    {
        parent::__construct();

        $this->bundle = $bundle;
    }

    /**
     * Installs bundle
     *
     * @return void
     * @throws \Exception
     */
    public function install(): void
    {
        $this->ensureCli();

        if (false === $this->isNoInteractionMode()) {
            $this->outputManualInstall();
        }
        $this->automaticInstall();
    }

    /**
     * Uninstalls bundle
     *
     * @return void
     * @throws \Exception
     */
    public function uninstall(): void
    {
        $this->ensureCli(false);

        if (false === $this->isNoInteractionMode()) {
            $this->outputManualUninstall();
        }
        $this->automaticUninstall();
    }

    /**
     * Returns if bundle is installed
     *
     * @return bool
     * @throws \Exception
     */
    public function isInstalled(): bool
    {
        return $this->isPimPrintFirewallConfigured();
    }

    /**
     * Pimcore needs reload after install
     *
     * @return bool
     */
    public function needsReloadAfterInstall(): bool
    {
        return false;
    }

    /**
     * Bundle can be installed
     *
     * @return bool
     * @throws \Exception
     */
    public function canBeInstalled(): bool
    {
        return !$this->isInstalled();
    }

    /**
     * Bundle can be uninstalled
     *
     * @return bool
     * @throws \Exception
     */
    public function canBeUninstalled(): bool
    {
        return $this->isInstalled();
    }

    /**
     * Assures that installer is started via cli.
     * Otherwise, it outputs an error and stops process.
     *
     * @param bool $install
     *
     * @return void
     * @throws \Exception
     */
    private function ensureCli(bool $install = true): void
    {
        if ('cli' === php_sapi_name()) {
            return;
        }

        $text = 'installed';
        $command = 'pimcore:bundle:install';
        if (!$install) {
            $text = 'uninstalled';
            $command = 'pimcore:bundle:uninstall';
        }

        $message = $this->bundle->getName() . ' must be ' . $text . ' via cli.<br>' . PHP_EOL;
        $message .= 'Please issue: bin/console ' . $command . ' ' . $this->bundle->getName();

        throw new InstallationException($message);
    }

    /**
     * Returns filepath to security.yaml
     *
     * @return string
     */
    private function getSecurityConfigFile(): string
    {
        $securityConfigFile = PIMCORE_PROJECT_ROOT . '/config/packages/security.yaml';

        if (!file_exists($securityConfigFile)) {
            $message = 'No security.yaml file not found at: ' . $securityConfigFile . PHP_EOL;
            $message .= 'Please check your Pimcore installation.';

            throw new InstallationException($message);
        }

        return $securityConfigFile;
    }

    /**
     * Returns parsed security.yaml as configuration array
     *
     * @return array
     */
    private function getSecurityConfig(): array
    {
        if (!isset($this->securityConfig)) {
            $this->securityConfig = Yaml::parseFile($this->getSecurityConfigFile());
            try {
                if (!is_array($this->securityConfig) || empty($this->securityConfig)) {
                    throw new \Exception();
                }
                if (!isset($this->securityConfig['security'])) {
                    throw new \Exception();
                }
            } catch (\Exception) {
                $message = 'No security configuration found in: ' . $this->getSecurityConfigFile() . PHP_EOL;
                $message .= 'Please check your Pimcore installation.';

                throw new InstallationException($message);
            }
        }

        return $this->securityConfig;
    }

    /**
     * Returns true if the pimprint_api section is present in security.yaml
     *
     * @return bool
     * @throws \Exception
     */
    private function isPimPrintFirewallConfigured(): bool
    {
        $config = $this->getSecurityConfig();

        return isset($config['security']['firewalls']['pimprint_api']);
    }

    /**
     * Returns true if the executed pimcore:bundle:install command is run in no-interaction mode
     *
     * @return bool
     */
    private function isNoInteractionMode(): bool
    {
        $definition = new InputDefinition(
            [
                new InputArgument('command', InputArgument::REQUIRED),
                new InputArgument('bundle', InputArgument::REQUIRED),
                new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE),
            ]
        );
        $arguments = new ArgvInput(null, $definition);

        return $arguments->getOption('no-interaction');
    }

    /**
     * Outputs information for manual install
     *
     * @return void
     * @throws \Exception
     */
    private function outputManualInstall(): void
    {
        $message = $this->bundle->getName() . ' needs the \'pimprint_api\' security firewall configuration.';
        $message .= PHP_EOL . PHP_EOL;
        $message .= 'We recommend to add the following firewall definition manually to your security.yaml ';
        $message .= 'right after the \'pimcore_admin\' firewall located in:';
        $message .= PHP_EOL . $this->getSecurityConfigFile() . PHP_EOL . PHP_EOL;

        $firewall = "# mds PimPrint api
pimprint_api:
    pattern: ^/pimprint-api
    stateless: true
    provider: pimcore_admin
    guard:
        entry_point: Mds\PimPrint\CoreBundle\Security\Guard\AdminSessionAuthenticator
        authenticators:
            - Mds\PimPrint\CoreBundle\Security\Guard\InDesignAuthenticator
            - Mds\PimPrint\CoreBundle\Security\Guard\AdminSessionAuthenticator";

        if ($this->isAuthenticatorManagerActive()) {
            $firewall = "# mds PimPrint api
pimprint_api:
    pattern: ^/pimprint-api
    stateless: true
    provider: pimcore_admin
    entry_point: Mds\PimPrint\CoreBundle\Security\Authenticator\AdminSessionAuthenticator
    custom_authenticators:
        - Mds\PimPrint\CoreBundle\Security\Authenticator\AdminSessionAuthenticator
        - Mds\PimPrint\CoreBundle\Security\Authenticator\InDesignAuthenticator";
        }

        $message .= $firewall . PHP_EOL . PHP_EOL;
        $message .= 'To have this installer add the \'pimprint_api\' firewall to your security configuration, ';
        $message .= 'please run this installation process in non-interaction mode by issuing: ' . PHP_EOL;
        $message .= 'bin/console pimcore:bundle:install MdsPimPrintCoreBundle -n' . PHP_EOL . PHP_EOL;
        $message .= 'ATTENTION:' . PHP_EOL;
        $message .= 'Do not be surprised that your security.yaml looks ugly after automatic installation!' . PHP_EOL;
        $message .= '\Symfony\Component\Yaml\Yaml::dump() sometimes creates really ugly files.' . PHP_EOL . PHP_EOL;
        $message .= 'For more information please refer: ';
        $message .= 'https://pimprint.mds.eu/docs/Getting_Started/Installation.html';

        throw new InstallationException($message);
    }

    /**
     * Adds pimprint_api firewall configuration and writes updates security.yaml file
     *
     * @return void
     * @throws \Exception
     */
    private function automaticInstall(): void
    {
        $config = $this->getSecurityConfig();

        if (isset($config['security']['firewalls']['pimprint_api'])) {
            throw new InstallationException('pimprint_api security firewall already installed.');
        }

        $firewalls = [];
        foreach ($config['security']['firewalls'] as $key => $firewall) {
            $firewalls[$key] = $firewall;
            if ('pimcore_admin' == $key) {
                if ($this->isAuthenticatorManagerActive()) {
                    $firewalls['pimprint_api'] = [
                        'pattern'               => '^/pimprint-api',
                        'stateless'             => true,
                        'provider'              => 'pimcore_admin',
                        'entry_point'           => 'Mds\PimPrint\CoreBundle\Security\Authenticator\AdminSessionAuthenticator',
                        'custom_authenticators' => [
                            'Mds\PimPrint\CoreBundle\Security\Authenticator\AdminSessionAuthenticator',
                            'Mds\PimPrint\CoreBundle\Security\Authenticator\InDesignAuthenticator',
                        ]
                    ];
                } else {
                    $firewalls['pimprint_api'] = [
                        'pattern'   => '^/pimprint-api',
                        'stateless' => true,
                        'provider'  => 'pimcore_admin',
                        'guard'     => [
                            'entry_point'    => 'Mds\PimPrint\CoreBundle\Security\Guard\AdminSessionAuthenticator',
                            'authenticators' => [
                                'Mds\PimPrint\CoreBundle\Security\Guard\InDesignAuthenticator',
                                'Mds\PimPrint\CoreBundle\Security\Guard\AdminSessionAuthenticator',
                            ]
                        ]
                    ];
                }
            }
        }

        $config['security']['firewalls'] = $firewalls;

        $this->writeSecurityYaml($config);
    }

    /**
     * Outputs information for manual uninstall
     *
     * @return void
     * @throws \Exception
     */
    private function outputManualUninstall(): void
    {
        $message = $this->bundle->getName() . ' has the \'pimprint_api\' security firewall configuration.';
        $message .= PHP_EOL . PHP_EOL;
        $message .= 'We recommend to remove the firewall definition manually from your security.yaml ';
        $message .= 'firewall located in:';
        $message .= PHP_EOL . $this->getSecurityConfigFile() . PHP_EOL . PHP_EOL;

        $message .= 'To have this uninstaller remove the \'pimprint_api\' firewall from your security configuration, ';
        $message .= 'please run this uninstallation process in non-interaction mode by issuing: ' . PHP_EOL;
        $message .= 'bin/console pimcore:bundle:uninstall MdsPimPrintCoreBundle -n' . PHP_EOL . PHP_EOL;
        $message .= 'ATTENTION:' . PHP_EOL;
        $message .= 'Do not be surprised that your security.yaml looks ugly after automatic uninstallation!' . PHP_EOL;
        $message .= '\Symfony\Component\Yaml\Yaml::dump() sometimes creates really ugly files.' . PHP_EOL . PHP_EOL;

        throw new InstallationException($message);
    }

    /**
     * Removes pimprint_api firewall configuration and writes updates security.yaml file
     *
     * @return void
     * @throws \Exception
     */
    private function automaticUninstall(): void
    {
        $config = $this->getSecurityConfig();

        if (!isset($config['security']['firewalls']['pimprint_api'])) {
            return;
        }

        unset($config['security']['firewalls']['pimprint_api']);

        $this->writeSecurityYaml($config);
    }

    /**
     * Writes $config to security.yml
     *
     * @param array $config
     *
     * @return void
     * @throws \Exception
     */
    private function writeSecurityYaml(array $config): void
    {
        @file_put_contents(
            $this->getSecurityConfigFile(),
            Yaml::dump($config, 5)
        );
    }

    /**
     * Returns true if security enable_authenticator_manager is activated.
     *
     * @return bool
     * @throws \Exception
     */
    private function isAuthenticatorManagerActive(): bool
    {
        $config = $this->getSecurityConfig();
        if (isset($config['security']['enable_authenticator_manager'])) {
            return (bool)$config['security']['enable_authenticator_manager'];
        }

        return false;
    }
}
