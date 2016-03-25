<?php

namespace Alpixel\Bundle\CMSBundle\Listener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\SecurityContext;

class KernelListener
{
    private $requestStack;
    private $tokenStorage;
    private $secret;
    private $authorizationChecker;

    public function __construct(RequestStack $requestStack, TokenStorage $tokenStorage, AuthorizationChecker $authorizationChecker, $secret)
    {
        $this->tokenStorage = $tokenStorage;
        $this->secret = $secret;
        $this->authorizationChecker = $authorizationChecker;
        $this->requestStack = $requestStack;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $this->requestStack->getMasterRequest();
        $route = $request->attributes->get('_route');
        $cookies = $request->cookies;

        $token = $this->tokenStorage->getToken();

        if ($token !== null && $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $cookie = new Cookie('can_edit', hash('sha256', 'can_edit' . $this->secret), 0, '/', null, false, false);
            $response->headers->setCookie($cookie);
        } elseif (!in_array($route, ['_profiler', '_wdt']) && $cookies->has('can_edit2')) {
            $response->headers->clearCookie('can_edit', '/');
        }
    }
}
