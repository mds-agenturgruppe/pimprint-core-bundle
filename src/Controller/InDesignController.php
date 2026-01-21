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

namespace Mds\PimPrint\CoreBundle\Controller;

use League\Flysystem\FilesystemException;
use Mds\PimPrint\CoreBundle\InDesign\CustomField\Search;
use Mds\PimPrint\CoreBundle\Service\JsonRequestDecoder;
use Mds\PimPrint\CoreBundle\Service\PluginParameters;
use Mds\PimPrint\CoreBundle\Service\PluginResponseCreator;
use Mds\PimPrint\CoreBundle\Service\ProjectsManager;
use Pimcore\Controller\FrontendController;
use Pimcore\Http\RequestHelper;
use Pimcore\Security\User\UserLoader;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class InDesignController
 *
 * @SuppressWarnings("PHPMD.CouplingBetweenObjects")
 *
 * @package Mds\PimPrint\CoreBundle\Controller
 */
class InDesignController extends FrontendController
{
    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();

        $services[UserLoader::class] = UserLoader::class;
        $services[ProjectsManager::class] = ProjectsManager::class;
        $services[PluginResponseCreator::class] = PluginResponseCreator::class;

        return $services;
    }

    /**
     * Returns a list of registered projects.
     *
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     * @throws NotFoundExceptionInterface
     */
    #[Route("/projects")]
    public function projectsAction(): JsonResponse
    {
        try {
            $this->ensureUser();

            return $this->pluginResponseCreator()
                        ->success(
                            [
                                'projects' => $this->projectsManager()
                                                   ->getProjectsInfo(),
                            ]
                        );
        } catch (\Exception $exception) {
            return $this->pluginResponseCreator()
                        ->error($exception);
        }
    }

    /**
     * Returns details for a project.
     *
     * @param string $identifier
     *
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     * @throws NotFoundExceptionInterface
     */
    #[Route("/project/{identifier}")]
    public function projectAction(string $identifier): JsonResponse
    {
        try {
            $this->ensureUser();
            $project = $this->projectsManager()
                            ->projectServiceFactory($identifier);

            $pluginElements = $project->config()
                                      ->offsetGet('plugin_elements');

            return $this->pluginResponseCreator()
                        ->success(
                            [
                                'formFields'   => $project->getFormFieldsConfig(),
                                'languages'    => $project->getLanguages(),
                                'publications' => $pluginElements['publications']['show'] //
                                    ? $project->getPublicationsTree() //
                                    : [],
                            ]
                        );
        } catch (\Exception $exception) {
            return $this->pluginResponseCreator()
                        ->error($exception);
        }
    }

    /**
     * Executes a project InDesign execution.
     *
     * @param string           $identifier
     * @param RequestHelper    $requestHelper
     * @param PluginParameters $pluginParams
     *
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws FilesystemException
     * @throws NotFoundExceptionInterface
     */
    #[Route("/project/{identifier}/run")]
    public function executeProjectAction(
        string $identifier,
        RequestHelper $requestHelper,
        PluginParameters $pluginParams
    ): JsonResponse {
        try {
            $this->ensureUser();
            $requestHelper->getRequest()
                          ->setLocale($pluginParams->get(PluginParameters::PARAM_LANGUAGE));
            $project = $this->projectsManager()
                            ->projectServiceFactory($identifier);

            return $this->pluginResponseCreator()
                        ->success(
                            [
                                'commands'    => $project->run(),
                                'preProcess'  => [],
                                'postProcess' => [],
                            ]
                        );
        } catch (\Exception $exception) {
            return $this->pluginResponseCreator()
                        ->error($exception);
        }
    }

    /**
     * Delivers templateFile for project identifier.
     *
     * @param string $identifier
     * @param string $templateFile
     *
     * @return BinaryFileResponse|NotFoundHttpException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route("/project/{identifier}/template/{templateFile}", name: 'mds_pimprint_downlaod_template')]
    public function downloadTemplateAction(
        string $identifier,
        string $templateFile
    ): BinaryFileResponse|NotFoundHttpException {
        try {
            $this->ensureUser();
            $project = $this->projectsManager()
                            ->projectServiceFactory($identifier);
            $filePath = $project->getTemplateFilePath($templateFile);
            if (!file_exists($filePath)) {
                throw new \Exception();
            }
            $stream = new Stream($filePath);
            $response = new BinaryFileResponse($stream);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($filePath));

            return $response;
        } catch (\Exception) {
            return $this->createNotFoundException();
        }
    }

    /**
     * Generic end point for search custom fields search execution
     *
     * @param Request            $request
     * @param JsonRequestDecoder $requestDecoder
     * @param string             $identifier
     * @param string             $customField
     *
     * @return JsonResponse
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws FilesystemException
     */
    #[Route("/project/{identifier}/custom-search/{customField}", name: 'mds_pimprint_custom_search')]
    public function customFieldSearchAction(
        Request $request,
        JsonRequestDecoder $requestDecoder,
        string $identifier,
        string $customField
    ): JsonResponse {
        try {
            $this->ensureUser();
            $requestDecoder->decode($request);
            $project = $this->projectsManager()
                            ->projectServiceFactory($identifier);

            $field = $project->getCustomFormField($customField);
            if (!$field instanceof Search) {
                throw new \Exception('Custom search field must be instance of: ' . Search::class);
            }

            return $this->json($field->getSearchResponse($request));
        } catch (\Exception $exception) {
            return $this->pluginResponseCreator()
                        ->error($exception);
        }
    }

    /**
     * Ensures a user is logged in.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    private function ensureUser(): void
    {
        $user = $this->userLoader()
                     ->getUser();
        if ($user) {
            return;
        }

        throw new \Exception('Unable to load user. PimPrint security firewall may not be configured correctly.');
    }

    /**
     * Returns Pimcore UserLoader
     *
     * @return UserLoader
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function userLoader(): UserLoader
    {
        return $this->container->get(UserLoader::class);
    }

    /**
     * Returns ProjectsManager
     *
     * @return ProjectsManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function projectsManager(): ProjectsManager
    {
        return $this->container->get(ProjectsManager::class);
    }

    /**
     * Returns PluginResponseCreator
     *
     * @return PluginResponseCreator
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function pluginResponseCreator(): PluginResponseCreator
    {
        return $this->container->get(PluginResponseCreator::class);
    }
}
