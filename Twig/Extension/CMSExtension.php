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
        ];
    }

    public function getFunctions()
    {
        return [
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
    
}
