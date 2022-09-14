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

use Mds\PimPrint\CoreBundle\Security\Guard\InDesignAuthenticator as InDesignGuard;
use Mds\PimPrint\CoreBundle\Security\Traits\InDesignRequestDetector;
use Mds\PimPrint\CoreBundle\Service\JsonRequestDecoder;
use Mds\PimPrint\CoreBundle\Session\PimPrintSessionBagConfigurator;
use Pimcore\Bundle\AdminBundle\Security\Authenticator\AdminLoginAuthenticator as PimcoreAdminLoginAuthenticator;
use Pimcore\Model\User;
use Pimcore\Tool\Authentication;
use Pimcore\Tool\Session;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PreAuthenticatedUserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class InDesignAuthenticator
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @package Mds\PimPrint\CoreBundle\Security\Authenticator
 */
class InDesignAuthenticator extends PimcoreAdminLoginAuthenticator
{
    use InDesignRequestDetector;

    /**
     * JsonRequestDecoder
     *
     * @var JsonRequestDecoder
     */
    private JsonRequestDecoder $jsonRequestDecoder;

    /**
     * InDesignAuthenticator constructor.
     *
     * @param EventDispatcherInterface $dispatcher
     * @param RouterInterface          $router
     * @param TranslatorInterface      $translator
     * @param JsonRequestDecoder       $jsonRequestDecoder
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        RouterInterface $router,
        TranslatorInterface $translator,
        JsonRequestDecoder $jsonRequestDecoder
    ) {
        parent::__construct($dispatcher, $router, $translator);

        $this->jsonRequestDecoder = $jsonRequestDecoder;
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

        $username = $request->get(InDesignGuard::PARAM_USERNAME);
        $password = $request->get(InDesignGuard::PARAM_PASSWORD);

        if ($username && $password) {
            $passport = parent::authenticate($request);
            $session = Session::get(PimPrintSessionBagConfigurator::NAMESPACE);
            $session->set('sendId', true);

            return $passport;
        } elseif (Session::requestHasSessionId($request, true)) {
            return $this->authenticateSession($request);
        } else {
            throw new AuthenticationException('', InDesignGuard::ERROR_CODE_NO_CREDENTIALS);
        }
    }

    /**
     * Authenticates against session.
     *
     * @param Request $request
     *
     * @return SelfValidatingPassport
     * @see \Pimcore\Bundle\AdminBundle\Security\Authenticator\AdminSessionAuthenticator::authenticate
     */
    public function authenticateSession(Request $request): SelfValidatingPassport
    {
        if (!Session::requestHasSessionId($request, true)) {
            throw new AuthenticationException('No session id');
        }
        if ('POST' == $request->getMethod()) {
            @session_id(Session::getSessionIdFromRequest($request, true));
        }

        $user = Authentication::authenticateSession($request);
        if (!$user instanceof User) {
            throw new AuthenticationException('Invalid User!');
        }

        $session = Session::getReadOnly();
        if ($session->has('2fa_required') && $session->get('2fa_required') === true) {
            $this->twoFactorRequired = true;
        }

        $badges = [
            new PreAuthenticatedUserBadge(),
        ];

        return new SelfValidatingPassport(
            new UserBadge($user->getUsername()),
            $badges
        );
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
        switch ($exception->getCode()) {
            case InDesignGuard::ERROR_CODE_NO_CREDENTIALS:
                $message = 'Please provide username and password.';
                break;

            default:
                $message = 'Invalid username or password.';
        }

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
     * @param string         $providerKey
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return null;
    }
}
