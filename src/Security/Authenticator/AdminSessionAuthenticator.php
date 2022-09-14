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

namespace Mds\PimPrint\CoreBundle\Security\Authenticator;

use Mds\PimPrint\CoreBundle\Security\Traits\InDesignRequestDetector;
use Pimcore\Bundle\AdminBundle\Security\Authenticator\AdminSessionAuthenticator as PimcoreAdminSessionAuthenticator;
use Pimcore\Tool\Authentication;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Class AdminSessionAuthenticator
 *
 * @package Security\Authenticator
 */
class AdminSessionAuthenticator extends PimcoreAdminSessionAuthenticator implements AuthenticationEntryPointInterface
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
     * Create a passport for the current request.
     *
     * @param Request $request
     *
     * @return Passport
     */
    public function authenticate(Request $request): Passport
    {
        $this->user = Authentication::authenticateSession($request);

        return parent::authenticate($request);
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
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }

    /**
     * Returns authorization required message.
     *
     * @return string
     */
    private function getLoginMessage(): string
    {
        return 'Authentication Required. Please login at <a href="/admin">Pimcore admin</a>.';
    }
}
