<?php
/**
 * mds. Agenturgruppe GmbH
 *
 * This source file is available under the terms of the
 * mds. Commercial License (MCL)
 *
 * Full copyright and license information is available in
 * LICENSE.md, which is distributed with this source code.
 *
 * @copyright Copyright (c) mds. Agenturgruppe GmbH (https://www.mds.eu)
 * @license   mds. Commercial License (MCL)
 */

namespace Mds\PimPrint\CoreBundle\Service;

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\InDesign\Traits\MissingAssetNotifierTrait;
use Mds\PimPrint\CoreBundle\Session\PimPrintSessionBagConfigurator;
use Pimcore\Http\RequestHelper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
     * PluginResponseCreator constructor.
     *
     * @param RequestHelper   $requestHelper
     * @param ProjectsManager $projectsManager
     */
    public function __construct(
        private readonly RequestHelper $requestHelper,
        private readonly ProjectsManager $projectsManager
    ) {
    }

    /**
     * Builds a success (success true) response for InDesign.
     *
     * @param array $data
     *
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     * @throws NotFoundExceptionInterface
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
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     * @throws NotFoundExceptionInterface
     */
    public function error(\Exception $exception): JsonResponse
    {
        $data = [
            'success'  => false,
            'messages' => [$exception->getMessage()]
        ];
        if ($this->isDebugMode()) {
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
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    protected function buildResponse(array $data, int $status = Response::HTTP_OK): JsonResponse
    {
        $headers['content-type'] = 'application/json;charset=utf-8';
        if (!isset($data['messages'])) {
            $data['messages'] = [];
        }
        $data['debugMode'] = $this->isDebugMode();
        $this->addMissingAssetPreMessage();
        $this->addMessages($data);
        if ($data['success']) {
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

            if ($config['debug']) {
                $debugIps = $config['debug_ip'] ?? '';
                if (empty($debugIps)) {
                    self::$isDebugMode = true;
                } else {
                    $debugIps = explode(',', $debugIps);
                    $clientIp = $this->requestHelper->getRequest()
                                                    ->getClientIp();
                    if (in_array($clientIp, $debugIps)) {
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function addImages(array &$data): void
    {
        try {
            $project = $this->projectsManager->getProject();
            if (
                !$project->config()
                         ->isAssetDownloadEnabled()
            ) {
                return;
            }
            $data['images'] = $project->commandQueue()
                                      ->getRegisteredAssets();
        } catch (\Exception) {
            return;
        }
    }
}
