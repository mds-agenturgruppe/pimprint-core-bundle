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

namespace Mds\PimPrint\CoreBundle\Service;

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\InDesign\Traits\MissingAssetNotifierTrait;
use Mds\PimPrint\CoreBundle\Session\PimPrintSessionBagConfigurator;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

/**
 * Class PluginResponseCreator
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class PluginResponseCreator
{
    use MissingAssetNotifierTrait;

    /**
     * Lazy loading.
     *
     * @var bool|null
     */
    private static ?bool $isDebugMode = null;

    /**
     * Pimcore RequestHelper.
     *
     * @var RequestHelper
     */
    private RequestHelper $requestHelper;

    /**
     * PimPrint ProjectsManager
     *
     * @var ProjectsManager
     */
    private ProjectsManager $projectsManager;

    /**
     * PluginResponseCreator constructor.
     *
     * @param RequestHelper   $requestHelper
     * @param ProjectsManager $projectsManager
     */
    public function __construct(RequestHelper $requestHelper, ProjectsManager $projectsManager)
    {
        $this->requestHelper = $requestHelper;
        $this->projectsManager = $projectsManager;
    }

    /**
     * Builds a success (success true) response for InDesign.
     *
     * @param array $data
     *
     * @return JsonResponse
     * @throws FilesystemException
     */
    public function success(array $data): JsonResponse
    {
        $data['success'] = true;

        return $this->buildResponse($data);
    }

    /**
     * Builds an error (success false) response for InDesign plugin with exception information.
     *
     * @param \Exception $exception
     *
     * @return JsonResponse
     */
    public function error(\Exception $exception): JsonResponse
    {
        $data = [
            'success'  => false,
            'messages' => [$exception->getMessage()]
        ];
        if (true === $this->isDebugMode()) {
            $data['messages'][] = $exception->getTraceAsString();
        }

        return $this->buildResponse($data, Response::HTTP_ACCEPTED);
    }

    /**
     * Adds charset to header and builds json response with $data as payload.
     *
     * @param array $data
     * @param int   $status
     *
     * @return JsonResponse
     * @throws \Exception
     * @throws FilesystemException
     */
    protected function buildResponse(array $data, int $status = Response::HTTP_OK): JsonResponse
    {
        $headers['content-type'] = 'application/json;charset=utf-8';
        if (false === isset($data['messages'])) {
            $data['messages'] = [];
        }
        $data['debugMode'] = $this->isDebugMode();
        $this->addMissingAssetPreMessage();
        $this->addMessages($data);
        if (true === $data['success']) {
            $this->addImages($data);
            $this->addSettings($data);
        }
        $this->addSession($data);

        return new JsonResponse($data, $status, $headers);
    }

    /**
     * Returns true if debug mode is enabled and optional ip is matching.
     *
     * @return bool
     */
    protected function isDebugMode(): bool
    {
        if (null === self::$isDebugMode) {
            self::$isDebugMode = false;
            $debugModeFile = PIMCORE_CONFIGURATION_DIRECTORY . '/debug-mode.php';
            $debugMode = [];
            if (file_exists($debugModeFile)) {
                $debugMode = include $debugModeFile;
            }
            $config['debug'] = $debugMode['active'] ?? false;
            $config['debug_ip'] = $debugMode['ip'] ?? '';

            if (true === $config['debug']) {
                $debugIps = $config['debug_ip'] ?? '';
                if (true === empty($debugIps)) {
                    self::$isDebugMode = true;
                } else {
                    $debugIps = explode(',', $debugIps);
                    $clientIp = $this->requestHelper->getRequest()
                                                    ->getClientIp();
                    if (true === in_array($clientIp, $debugIps)) {
                        self::$isDebugMode = true;
                    }
                }
            }
        }

        return self::$isDebugMode;
    }

    /**
     * Adds project settings if a project is currently selected.
     *
     * @param array $data
     *
     * @return void
     * @throws \Exception
     * @throws FilesystemException
     */
    private function addSettings(array &$data): void
    {
        try {
            $project = $this->projectsManager->getProject();
        } catch (\Exception) {
            return;
        }
        $data['settings'] = $project->getSettings();
    }

    /**
     * Adds sessionId to JSON response $data if a login session was created for InDesign.
     *
     * @param array $data
     *
     * @return void
     */
    private function addSession(array &$data): void
    {
        $request = $this->requestHelper->getMainRequest();
        try {
            $session = $request->getSession();
        } catch (\Exception) {
            return;
        }

        $sessionBag = $session->getBag(PimPrintSessionBagConfigurator::NAMESPACE);
        if (!$sessionBag instanceof AttributeBag) {
            return;
        }
        if (!$sessionBag->has('sendId')) {
            return;
        }

        $data['session'] = [
            'name' => $session->getName(),
            'id'   => $session->getId(),
        ];
        $sessionBag->remove('sendId');
    }

    /**
     * Adds project preMessages to $data.
     *
     * @param array $data
     *
     * @return void
     */
    private function addMessages(array &$data): void
    {
        try {
            $messages = $this->projectsManager->getProject()
                                              ->getPreMessages();
            if (empty($messages)) {
                throw new \Exception();
            }
        } catch (\Exception) {
            return;
        }
        $data['messages'] = array_merge(
            $data['messages'],
            $messages
        );
    }

    /**
     * Adds used images.
     *
     * @param array $data
     *
     * @return void
     */
    private function addImages(array &$data): void
    {
        try {
            $project = $this->projectsManager->getProject();
            if (false === $project->config()
                                  ->isAssetDownloadEnabled()) {
                return;
            }
            $data['images'] = $project->getCommandQueue()
                                      ->getRegisteredAssets();
        } catch (\Exception) {
            return;
        }
    }
}
