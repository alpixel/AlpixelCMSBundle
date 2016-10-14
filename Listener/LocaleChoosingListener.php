<?php

namespace Alpixel\Bundle\CMSBundle\Listener;

use JMS\I18nRoutingBundle\Router\LocaleResolverInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;


/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class LocaleChoosingListener
{
    /**
     * @var string
     */
    private $defaultLocale;
    /**
     * @var array
     */
    private $locales;
    /**
     * @var \JMS\I18nRoutingBundle\Router\LocaleResolverInterface
     */
    private $localeResolver;

    /**
     * LocaleChoosingListener constructor.
     * @param $defaultLocale
     * @param array $locales
     * @param \JMS\I18nRoutingBundle\Router\LocaleResolverInterface $localeResolver
     */
    public function __construct($defaultLocale, array $locales, LocaleResolverInterface $localeResolver)
    {
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
        $this->localeResolver = $localeResolver;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        if ('' !== rtrim($request->getPathInfo(), '/')) {
            return;
        }

        $ex = $event->getException();
        if (!$ex instanceof NotFoundHttpException || !$ex->getPrevious() instanceof ResourceNotFoundException) {
            return;
        }

        $locale = $this->localeResolver->resolveLocale($request, $this->locales) ?: $this->defaultLocale;
        $request->setLocale($locale);

        $params = $request->query->all();
        unset($params['hl']);

        $event->setResponse(
            new RedirectResponse(
                $request->getBaseUrl().'/'.$locale.'/'.($params ? '?'.http_build_query($params) : ''),
                301
            )
        );
    }
}
