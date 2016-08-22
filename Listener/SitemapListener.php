<?php

namespace Alpixel\Bundle\CMSBundle\Listener;

use Doctrine\ORM\EntityManager;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\GoogleMultilangUrlDecorator;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class SitemapListener implements SitemapListenerInterface
{
    private $entityManager;
    private $router;
    private $defaultLocale;
    private $locales;
    private $baseUrl;
    private $contentTypes;

    public function __construct(
        RouterInterface $router,
        EntityManager $entityManager,
        $defaultLocale,
        $locales,
        $baseUrl,
        $contentTypes
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
        $this->baseUrl = $baseUrl;
        $this->contentTypes = $contentTypes;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();

        if (is_null($section) || $section == 'cms') {
            foreach ($this->locales as $locale) {
                $nodeRepository = $this->entityManager->getRepository('AlpixelCMSBundle:Node');
                $pages = $nodeRepository->findAllWithLocale($locale);

                foreach ($pages as $page) {
                    $hasController = true;
                    foreach ($this->contentTypes as $contentType) {
                        if (get_class($page) == $contentType['class'] && $contentType['controller'] === null) {
                            $hasController = false;
                        }
                    }

                    if ($hasController === false) {
                        continue;
                    }

                    $url = $this->router->generate(
                        'alpixel_cms',
                        [
                            'slug'    => $page->getSlug(),
                            '_locale' => $page->getLocale(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );

                    $url = new UrlConcrete(
                        $url,
                        $page->getDateUpdated(),
                        UrlConcrete::CHANGEFREQ_MONTHLY,
                        .7
                    );

                    $urlLang = new GoogleMultilangUrlDecorator($url);

                    $translations = $nodeRepository->findTranslations($page);
                    foreach ($translations as $translation) {
                        if ($locale !== $translation->getLocale()) {
                            $url = $this->router->generate(
                                'alpixel_cms',
                                [
                                    'slug'    => $translation->getSlug(),
                                    '_locale' => $translation->getLocale(),
                                ],
                                UrlGeneratorInterface::ABSOLUTE_URL
                            );
                            $urlLang->addLink($url, $translation->getLocale());
                        }
                    }

                    $event->getGenerator()->addUrl(
                        $urlLang,
                        'cms.'.$locale
                    );
                }
            }
        }
    }
}
