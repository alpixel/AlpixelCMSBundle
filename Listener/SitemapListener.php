<?php

namespace Alpixel\Bundle\CMSBundle\Listener;

use Doctrine\ORM\EntityManager;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\RouterInterface;

class SitemapListener implements SitemapListenerInterface
{
    private $entityManager;
    private $router;
    private $defaultLocale;
    private $locales;
    private $baseUrl;

    public function __construct(RouterInterface $router, EntityManager $entityManager, $defaultLocale, $locales, $baseUrl)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
        $this->baseUrl = $baseUrl;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();

        if (is_null($section) || $section == 'cms') {
            $nodeRepository = $this->entityManager->getRepository('AlpixelCMSBundle:Node');
            $pages = $nodeRepository->findAllWithLocale($this->defaultLocale);

            foreach ($pages as $page) {
                $url = $this->router->generate('alpixel_cms', [
                    'slug'    => $page->getSlug(),
                    '_locale' => $page->getLocale(),
                ]);

                $url = new UrlConcrete(
                    $url,
                    $page->getDateUpdated(),
                    UrlConcrete::CHANGEFREQ_MONTHLY,
                    .7
                );

                $urlLang = new GoogleMultilangUrlDecorator($url);
                foreach ($this->locales as $locale) {
                    if ($locale !== $this->defaultLocale) {
                        $translatedPage = $nodeRepository->findTranslation($page, $locale);

                        if ($translatedPage !== null) {
                            $url = $this->router->generate('alpixel_cms', [
                                'slug'    => $translatedPage->getSlug(),
                                '_locale' => $translatedPage->getLocale(),
                            ]);

                            $urlLang->addLink($url, $locale);
                        }
                    }
                }

                $event->getGenerator()->addUrl(
                    $urlLang,
                    'cms'
                );
            }
        }
    }
}
