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

use Pimcore\Model\User;
use Pimcore\Model\User\UserRole;
use Pimcore\Model\User\Workspace\DataObject as DataObjectWorkspace;
use Pimcore\Tool\Admin;

/**
 * Service to access the currently logged in User.
 *
 * @package Mds\PimPrint\CoreBundle\Service
 */
class UserHelper
{
    /**
     * User instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Lazy loading property.
     *
     * @var array
     */
    protected $visibleWorkspaceLanguages;

    /**
     * UserHelper constructor.
     */
    public function __construct()
    {
        $this->user = Admin::getCurrentUser();
    }

    /**
     * Returns current user.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns an array with all visible languages assigned to all DataObject workspaces for user and all its roles.
     * If user is admin, all activates languages are returned.
     *
     * @return array
     */
    public function getVisibleWorkspaceLanguages()
    {
        if (null !== $this->visibleWorkspaceLanguages) {
            return $this->visibleWorkspaceLanguages;
        }
        $user = $this->getUser();

        $languages = [];
        foreach ($user->getWorkspacesObject() as $workspace) {
            $languages = $this->getVisibleLanguageFromWorkspace($workspace);
        }

        foreach ($user->getRoles() as $roleId) {
            $role = UserRole::getById($roleId);
            if (false === $role instanceof UserRole) {
                continue;
            }
            foreach ($role->getWorkspacesObject() as $workspace) {
                $languages = array_merge(
                    $languages,
                    $this->getVisibleLanguageFromWorkspace($workspace)
                );
            }
        }
        $languages = array_filter(array_unique($languages));
        $index = array_search('default', $languages);
        if (false !== $index) {
            unset($languages[$index]);
        }
        $this->visibleWorkspaceLanguages = $languages;

        return $this->visibleWorkspaceLanguages;
    }

    /**
     * Returns visible languages assigned to $workspace.
     *
     * @param DataObjectWorkspace $workspace
     *
     * @return array
     */
    protected function getVisibleLanguageFromWorkspace(DataObjectWorkspace $workspace)
    {
        $languages = $workspace->getLView();

        return explode(',', $languages);
    }
}
