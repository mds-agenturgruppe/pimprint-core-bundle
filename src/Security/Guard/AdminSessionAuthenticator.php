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

namespace Mds\PimPrint\CoreBundle\Security\Guard;

use Pimcore\Bundle\AdminBundle\Security\User\User as AdminUser;
use Pimcore\Model\User;
use Pimcore\Tool\Authentication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Authenticator used for user authentication against Pimcore backend-session.
 * Only used in 'dev' environment and useful for developing and debugging PimPrint.
 *
 * @package Mds\PimPrint\CoreBundle\Security
 */
class AdminSessionAuthenticator extends AbstractAuthenticator
{
    /**
     * {@inheritDoc}
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return !$this->isInDesignRequest($request);
    }

    /**
     * {@inheritDoc}
     *
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new Response($this->getLoginMessage(), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Returns authorization required message.
     *
     * @return string
     */
    protected function getLoginMessage()
    {
        return 'Authentication Required. Please login at <a href="/admin">Pimcore admin</a>.';
    }

    /**
     * {@inheritDoc}
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        if ($pimcoreUser = Authentication::authenticateSession($request)) {
            return [
                'user' => $pimcoreUser
            ];
        }

        return [];
    }

    /**
     * {@inheritDoc}
     *
     * @param array                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!is_array($credentials)) {
            throw new AuthenticationException('Invalid credentials');
        }
        if (isset($credentials['user']) && $credentials['user'] instanceof User) {
            return new AdminUser($credentials['user']);
        }

        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @param array         $credentials
     * @param UserInterface $user
     *
     * @return bool
     * @see \Pimcore\Bundle\AdminBundle\Security\Guard\AdminAuthenticator::checkCredentials
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response($this->getLoginMessage(), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritDoc}
     *
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * {@inheritDoc}
     *
     * @return bool
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
