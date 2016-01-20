<?php

namespace Alpixel\Bundle\CMSBundle\Listener;

use Alpixel\Bundle\SEOBundle\Service\SitemapListenerInterface;
use Alpixel\Bundle\SEOBundle\Event\SitemapPopulateEvent;
use Alpixel\Bundle\SEOBundle\Sitemap\Url\UrlConcrete;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Routing\RouterInterface;

class SitemapListener implements SitemapListenerInterface
{
    protected $doctrine;
    private $router;

    public function __construct(Registry $doctrine, RouterInterface $router)
    {
        $this->doctrine = $doctrine;
        $this->router   = $router;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        $section = $event->getSection();
        if (is_null($section) || $section == 'cms') {

            $entities      = array();
            $entityManager = $this->doctrine->getManager();
            $meta          = $entityManager->getMetadataFactory()->getAllMetadata();

            foreach ($meta as $m) {
                $relations = $m->getAssociationMappings();
                if(array_key_exists('node', $relations) && $relations['node']['targetEntity'] == 'Alpixel\Bundle\CMSBundle\Entity\Node') {
                    $entities[] = $m;
                }
            }

            $pages = array();
            foreach($entities as $entity) {
                $objects = $entityManager
                            ->getRepository($entity->getName())
                            ->findAll()
                        ;
                foreach($objects as $object) {
                    if($object->getNode()->getPublished() === true) {
                        $pages[] = $object;
                    }
                }
            }

            foreach ($pages as $cms) {
                $url = $this->router->generate('front_cms', array(
                        'slug' => $cms->getNode()->getSlug()
                    ),
                    true
                );

                $event->getGenerator()->addUrl(
                    new UrlConcrete(
                        $url,
                        new \DateTime(),
                        UrlConcrete::CHANGEFREQ_MONTHLY,
                        .5
                    ),
                    'cms'
                );
            }
        }
    }
}
