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

use Mds\PimPrint\CoreBundle\Security\Traits\InDesignRequestDetector;
use Pimcore\Bundle\AdminBundle\Security\User\User as AdminUser;
use Pimcore\Model\User;
use Pimcore\Tool\Authentication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Authenticator used for user authentication against Pimcore backend-session.
 * Accessing the /pimprint-api/... routes via Browser is useful for developing and debugging PimPrint.
 *
 * @see \Mds\PimPrint\CoreBundle\Controller\InDesignController
 *
 * @package Mds\PimPrint\CoreBundle\Security
 */
class AdminSessionAuthenticator extends AbstractGuardAuthenticator
{
    use InDesignRequestDetector;

    /**
     * Does the authenticator support the given Request?
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return !$this->isInDesignRequest($request);
    }

    /**
     * Returns a response that directs the user to authenticate.
     *
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return Response
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new Response($this->getLoginMessage(), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Returns authorization required message.
     *
     * @return string
     */
    protected function getLoginMessage(): string
    {
        return 'Authentication Required. Please login at <a href="/admin">Pimcore admin</a>.';
    }

    /**
     * Get the authentication credentials from the request and return them as any type (e.g. an associate array).
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request): array
    {
        if ($pimcoreUser = Authentication::authenticateSession($request)) {
            return [
                'user' => $pimcoreUser
            ];
        }

        return [];
    }

    /**
     * Return a UserInterface object based on the credentials.
     *
     * @param array                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return UserInterface|AdminUser|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface|AdminUser|null
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
     * Returns true if the credentials are valid.
     *
     * @param array         $credentials
     * @param UserInterface $user
     *
     * @return bool
     * @see \Pimcore\Bundle\AdminBundle\Security\Guard\AdminAuthenticator::checkCredentials
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return false;
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new Response($this->getLoginMessage(), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication executed and was successful!
     *
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey
     *
     * @return null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return null;
    }

    /**
     * Does this method support remember me cookies?
     *
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
