<?php

namespace Alpixel\Bundle\CMSBundle\Twig\Extension;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Alpixel\Bundle\CMSBundle\Helper\CMSHelper;

class CMSExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
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
            new \Twig_SimpleFunction('cms_contentType_get_description', [$this, 'cmsGetDescription']),
        ];
    }

    public function cmsGetDescription(Node $node)
    {
        $contentType = $this->cmsHelper->getContentTypeFromNodeElementClass($node);
        if ($contentType !== null) {
            return $contentType['description'];
        }
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

    public function cmsHasTranslation(Node $node, $locale)
    {
        return $this->cmsHelper->nodeGetTranslation($node, $locale);
    }
}
