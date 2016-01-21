<?php

namespace Alpixel\Bundle\CMSBundle\Listener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;

class KernelListener
{
    private $requestStack;
    private $securityContext;
    private $secret;

    public function __construct(RequestStack $requestStack, SecurityContext $securityContext, $secret)
    {
        $this->securityContext = $securityContext;
        $this->secret = $secret;
        $this->requestStack = $requestStack;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $request = $this->requestStack->getMasterRequest();
        $route = $request->attributes->get('_route');
        $cookies = $request->cookies;
        $token = $this->securityContext->getToken();

        if ($token !== null && $this->securityContext->isGranted('ROLE_ADMIN')) {
            $cookie = new Cookie('can_edit', hash('sha256', 'can_edit'.$this->secret), 0, '/', null, false, false);
            $response->headers->setCookie($cookie);
        } else if (!in_array($route, ['_profiler', '_wdt']) && $cookies->has('can_edit2')) {
            $response->headers->clearCookie('can_edit', '/');
        }
    }
}
