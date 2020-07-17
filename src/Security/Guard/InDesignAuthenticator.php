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

use Mds\PimPrint\CoreBundle\Service\JsonRequestDecoder;
use Mds\PimPrint\CoreBundle\Session\PimPrintSessionBagConfigurator;
use Pimcore\Bundle\AdminBundle\Security\BruteforceProtectionHandler;
use Pimcore\Bundle\AdminBundle\Security\User\User as AdminUser;
use Pimcore\Model\User;
use Pimcore\Tool\Authentication;
use Pimcore\Tool\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Authenticator used for user authentication from InDesign-Plugin.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 *
 * @package Mds\PimPrint\CoreBundle\Security
 */
class InDesignAuthenticator extends AbstractAuthenticator
{
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
     * Pimcore BruteforceProtectionHandler instance.
     *
     * @var BruteforceProtectionHandler
     */
    protected $bruteforceProtectionHandler;

    /**
     * JsonRequestDecoder instance.
     *
     * @var JsonRequestDecoder
     */
    private $jsonRequestDecoder;

    /**
     * InDesignAuthenticator constructor.
     *
     * @param BruteforceProtectionHandler $bruteforceProtectionHandler
     * @param JsonRequestDecoder          $jsonRequestDecoder
     */
    public function __construct(
        BruteforceProtectionHandler $bruteforceProtectionHandler,
        JsonRequestDecoder $jsonRequestDecoder
    ) {
        $this->bruteforceProtectionHandler = $bruteforceProtectionHandler;
        $this->jsonRequestDecoder = $jsonRequestDecoder;
    }

    /**
     * {@inheritDoc}
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        if (true === $this->isInDesignRequest($request)) {
            @ini_set('session.use_only_cookies', 0);

            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @param Request                      $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
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
     * {@inheritDoc}
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request)
    {
        $this->jsonRequestDecoder->decode($request);
        $username = $request->get(self::PARAM_USERNAME);
        $password = $request->get(self::PARAM_PASSWORD);
        if ($username && $password) {
            $this->bruteforceProtectionHandler->checkProtection($username);
            $credentials = [
                'username' => $username,
                'password' => $password,
            ];
        } elseif ($pimcoreUser = $this->authenticateSession($request)) {
            return [
                'user' => $pimcoreUser
            ];
        } else {
            $this->bruteforceProtectionHandler->checkProtection();
            throw new AuthenticationException('', self::ERROR_CODE_NO_CREDENTIALS);
        }

        return $credentials;
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
        $user = null;
        $pimcoreUser = null;

        if (!is_array($credentials)) {
            throw new AuthenticationException('', self::ERROR_CODE_NO_CREDENTIALS);
        }
        if (true === isset($credentials['user']) && $credentials['user'] instanceof User) {
            $pimcoreUser = $credentials['user'];
        } elseif (false === isset($credentials['username']) && false === isset($credentials['password'])) {
            throw new AuthenticationException('', self::ERROR_CODE_NO_CREDENTIALS);
        } else {
            $pimcoreUser = Authentication::authenticatePlaintext($credentials['username'], $credentials['password']);
            if ($pimcoreUser instanceof User) {
                Session::useSession(
                    function (AttributeBagInterface $session) use ($pimcoreUser) {
                        Session::regenerateId();
                        $session->set('user', $pimcoreUser);
                        $session->set('sendId', true);
                    },
                    PimPrintSessionBagConfigurator::NAMESPACE
                );
            }
        }
        if ($pimcoreUser instanceof User) {
            $user = new AdminUser($pimcoreUser);
        }

        return $user;
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
     * @return JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $this->jsonRequestDecoder->decode($request);
        $this->bruteforceProtectionHandler->addEntry($request->get(self::PARAM_USERNAME), $request);

        switch ($exception->getCode()) {
            case self::ERROR_CODE_NO_CREDENTIALS:
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

    /**
     * Authenticates against PimPrint session.
     *
     * @param Request $request
     *
     * @return User|null
     * @see \Pimcore\Tool\Authentication::authenticateSession
     */
    public function authenticateSession(Request $request)
    {
        $this->jsonRequestDecoder->decode($request);
        if (!Session::requestHasSessionId($request, true)) {
            return null;
        }
        $session = Session::getReadOnly(PimPrintSessionBagConfigurator::NAMESPACE);
        $user = $session->get('user');

        if ($user instanceof User) {
            $user = User::getById($user->getId());
            if (Authentication::isValidUser($user)) {
                return $user;
            }
        }

        return null;
    }
}
