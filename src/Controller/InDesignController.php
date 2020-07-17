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
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/project/{ident}")
     *
     * @param string $ident
     *
     * @return JsonResponse
     */
    public function projectAction($ident)
    {
        try {
            $project = $this->projectsManager->projectServiceFactory($ident);

            return $this->pluginResponse->success(
                [
                    'languages'    => $project->getLanguages(),
                    'options'      => $project->getOptions(),
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
     * @Route("/project/{ident}/run")
     *
     * @param string           $ident
     * @param PluginParameters $pluginParams
     *
     * @return JsonResponse
     */
    public function executeProjectAction($ident, PluginParameters $pluginParams)
    {
        try {
            $this->requestHelper->getRequest()
                                ->setLocale($pluginParams->get(PluginParameters::PARAM_LANGUAGE));
            $project = $this->projectsManager->projectServiceFactory($ident);

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
}
