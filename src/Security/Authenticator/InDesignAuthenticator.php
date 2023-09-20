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
use Mds\PimPrint\CoreBundle\Service\JsonRequestDecoder;
use Mds\PimPrint\CoreBundle\Session\PimPrintSessionBagConfigurator;
use Pimcore\Security\User\UserChecker;
use Pimcore\Security\User\UserProvider;
use Pimcore\Tool\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PreAuthenticatedUserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Class InDesignAuthenticator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @package Mds\PimPrint\CoreBundle\Security\Authenticator
 */
class InDesignAuthenticator extends AbstractAuthenticator
{
    use InDesignRequestDetector;

    /**
     * HTTP username param.
     *
     * @var string
     */
    const PARAM_USERNAME = 'username';

    /**
     * HTTP password param.
     *
     * @var string
     */
    const PARAM_PASSWORD = 'password';

    /**
     * Exception code 'no credentials'.
     *
     * @var int
     */
    const ERROR_CODE_NO_CREDENTIALS = 10;

    /**
     * JsonRequestDecoder
     *
     * @var JsonRequestDecoder
     */
    private JsonRequestDecoder $jsonRequestDecoder;

    /**
     * Pimcore Security UserChecker
     *
     * @var UserChecker
     */
    private UserChecker $userChecker;

    /**
     * Pimcore Security UserProvider
     *
     * @var UserProvider
     */
    private UserProvider $userProvider;

    /**
     * InDesignAuthenticator constructor.
     *
     * @param UserChecker        $userChecker
     * @param UserProvider       $userProvider
     * @param JsonRequestDecoder $jsonRequestDecoder
     */
    public function __construct(
        UserChecker $userChecker,
        UserProvider $userProvider,
        JsonRequestDecoder $jsonRequestDecoder,
    ) {
        $this->jsonRequestDecoder = $jsonRequestDecoder;
        $this->userChecker = $userChecker;
        $this->userProvider = $userProvider;
    }

    /**
     * Does the authenticator support the given Request?
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        if (true === $this->isInDesignRequest($request)) {
            @ini_set('session.use_only_cookies', 0);

            return true;
        }

        return false;
    }

    /**
     * Returns a response that directs the user to authenticate.
     *
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse(
            [
                'success'   => false,
                'debugMode' => false,
                'messages'  => ['Authentication Required']
            ],
            Response::HTTP_UNAUTHORIZED
        );
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
        $this->jsonRequestDecoder->decode($request);

        $username = $request->get(self::PARAM_USERNAME);
        $password = $request->get(self::PARAM_PASSWORD);

        if ($username && $password) {
            $userBadge = new UserBadge($username, $this->userProvider->loadUserByIdentifier(...));
            $passport = new Passport($userBadge, new PasswordCredentials($password), [new RememberMeBadge()]);

            $session = Session::getSessionBag(
                $request->getSession(),
                PimPrintSessionBagConfigurator::NAMESPACE
            );
            $session->set('sendId', true);

            return $passport;
        }

        if (!empty($request->getSession()->getName())) {
            return $this->authenticateSession($request);
        }

        throw new AuthenticationException('', self::ERROR_CODE_NO_CREDENTIALS);
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $message = match ($exception->getCode()) {
            self::ERROR_CODE_NO_CREDENTIALS => 'Please provide username and password.',
            default => 'Invalid username or password.',
        };

        return new JsonResponse(
            [
                'success'   => false,
                'debugMode' => false,
                'messages'  => [$message]
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Called when authentication executed and was successful!
     *
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $firewallName
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * Authenticates against session.
     *
     * @param Request $request
     *
     * @return SelfValidatingPassport
     */
    private function authenticateSession(Request $request): SelfValidatingPassport
    {
        $this->initPostSession($request);

        $user = null;
        $session = $request->getSession();
        $session->start();

        $token = $session->get('_security_pimprint_api');
        $token = $token ? unserialize($token) : null;

        if ($token instanceof TokenInterface) {
            $token->setUser($this->userProvider->refreshUser($token->getUser()));
            $user = $token->getUser();
        }

        if (!$user instanceof UserInterface) {
            throw new AuthenticationException('Invalid User!');
        }

        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier()),
            [new PreAuthenticatedUserBadge()]
        );
    }

    /**
     * Initializes session for POST requests without cookie
     *
     * @param Request $request
     *
     * @return void
     */
    private function initPostSession(Request $request): void
    {
        if ('POST' != $request->getMethod()) {
            return;
        }
        $sessionId = $request->get(
            $request->getSession()
                    ->getName()
        );
        if (empty($sessionId)) {
            throw new AuthenticationException('No session id');
        }

        @session_id($sessionId);
    }
}
