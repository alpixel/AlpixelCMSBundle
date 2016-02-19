<?php

namespace Alpixel\Bundle\CMSBundle\Twig\Extension;

use Alpixel\Bundle\CMSBundle\Entity\NodeInterface;
use Alpixel\Bundle\CMSBundle\Helper\CMSHelper;

class CMSExtension extends \Twig_Extension
{
    protected $contentTypes;
    protected $container;
    protected $cmsHelper;

    public function __construct(CMSHelper $cmsHelper, $container, $contentTypes = null)
    {
        $this->cmsHelper = $cmsHelper;
        $this->container = $container;
        $this->contentTypes = $contentTypes;
    }

    public function getName()
    {
        return 'cms';
    }

    public function getGlobals()
    {
        return [
            'cms_contentTypes' => $this->contentTypes,
            'cms_languages'    => $this->container->getParameter('lunetics_locale.allowed_locales'),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cms_get_translation', [$this, 'cmsHasTranslation']),
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('iso_to_country_name', [$this, 'isoToCountryName']),
        ];
    }

    public function isoToCountryName($iso)
    {
        return \Locale::getDisplayLanguage($iso, $this->container->getParameter('default_locale'));
    }

    public function cmsHasTranslation(NodeInterface $node, $locale)
    {
        return $this->cmsHelper->nodeGetTranslation($node, $locale);
    }
}
