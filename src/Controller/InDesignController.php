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

namespace Mds\PimPrint\CoreBundle\Controller;

use Mds\PimPrint\CoreBundle\Service\PluginParameters;
use Mds\PimPrint\CoreBundle\Service\PluginResponseCreator;
use Mds\PimPrint\CoreBundle\Service\ProjectsManager;
use Pimcore\Controller\FrontendController;
use Pimcore\Http\RequestHelper;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class InDesignController
 *
 * @package Mds\PimPrint\CoreBundle\Controller
 */
class InDesignController extends FrontendController
{
    /**
     * Pimcore RequestHelper service.
     *
     * @var RequestHelper
     */
    private $requestHelper;

    /**
     * PimPrint ProjectsManager service.
     *
     * @var ProjectsManager
     */
    private $projectsManager;

    /**
     * PluginResponseCreator service.
     *
     * @var PluginResponseCreator
     */
    private $pluginResponse;

    /**
     * InDesignController constructor.
     *
     * @param RequestHelper         $requestHelper
     * @param ProjectsManager       $projectsManager
     * @param PluginResponseCreator $pluginResponse
     */
    public function __construct(
        RequestHelper $requestHelper,
        ProjectsManager $projectsManager,
        PluginResponseCreator $pluginResponse
    ) {
        $this->requestHelper = $requestHelper;
        $this->projectsManager = $projectsManager;
        $this->pluginResponse = $pluginResponse;
    }

    /**
     * Returns list of registered projects.
     *
     * @Route("/projects")
     *
     * @return JsonResponse
     */
    public function projectsAction()
    {
        try {
            return $this->pluginResponse->success(
                [
                    'projects' => $this->projectsManager->getProjectsInfo(),
                ]
            );
        } catch (\Exception $e) {
            return $this->pluginResponse->error($e);
        }
    }

    /**
     * Returns details for project.
     *
     * @Route("/project/{identifier}")
     *
     * @param string $identifier
     *
     * @return JsonResponse
     */
    public function projectAction(string $identifier)
    {
        try {
            $project = $this->projectsManager->projectServiceFactory($identifier);

            return $this->pluginResponse->success(
                [
                    'formFields'   => $project->getFormFields(),
                    'languages'    => $project->getLanguages(),
                    'publications' => $project->getPublicationsTree(),
                ]
            );
        } catch (\Exception $e) {
            return $this->pluginResponse->error($e);
        }
    }

    /**
     * Executes a project InDesign execution.
     *
     * @Route("/project/{identifier}/run")
     *
     * @param string           $identifier
     * @param PluginParameters $pluginParams
     *
     * @return JsonResponse
     */
    public function executeProjectAction(string $identifier, PluginParameters $pluginParams)
    {
        try {
            $this->requestHelper->getRequest()
                                ->setLocale($pluginParams->get(PluginParameters::PARAM_LANGUAGE));
            $project = $this->projectsManager->projectServiceFactory($identifier);

            return $this->pluginResponse->success(
                [
                    'commands'    => $project->run(),
                    'preProcess'  => [],
                    'postProcess' => [],
                ]
            );
        } catch (\Exception $e) {
            return $this->pluginResponse->error($e);
        }
    }

    /**
     * Delivers templateFile for project identifier.
     *
     * @Route("/project/{identifier}/template/{templateFile}", name="mds_pimprint_downlaod_template")
     *
     * @param string $identifier
     * @param string $templateFile
     *
     * @return BinaryFileResponse|NotFoundHttpException
     */
    public function downloadTemplateAction(string $identifier, string $templateFile)
    {
        try {
            $project = $this->projectsManager->projectServiceFactory($identifier);
            $filePath = $project->getTemplateFilePath($templateFile);
            if (false === file_exists($filePath)) {
                throw new \Exception();
            }
            $stream = new Stream($filePath);
            $response = new BinaryFileResponse($stream);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, basename($filePath));

            return $response;
        } catch (\Exception $e) {
            return $this->createNotFoundException();
        }
    }
}
