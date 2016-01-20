<?php

namespace Alpixel\Bundle\CMSBundle\Listener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{

  private $securityContext;
  private $request;
  private $secret;

  public function __construct(SecurityContext $securityContext, Request $request, $secret) {
    $this->securityContext = $securityContext;
    $this->request         = $request;
    $this->secret          = $secret;
  }

  /**
   * Do the magic.
   *
   * @param InteractiveLoginEvent $event
   */
  public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
  {
    if ($this->securityContext->isGranted('ROLE_ADMIN')) {
      setcookie ("can_edit", hash('sha256', 'can_edit'.$this->secret), 0, '/');
    }
  }
}
