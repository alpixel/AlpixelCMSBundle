<?php

namespace Alpixel\Bundle\CMSBundle\Twig\Extension;

use Doctrine\Bundle\DoctrineBundle\Registry;

class CMSExtension extends \Twig_Extension
{
    protected $doctrine;
    protected $contentTypes;
    protected $container;
    protected $request;

    public function __construct($container, Registry $doctrine, $contentTypes)
    {
        $this->container = $container;

        if ($this->container->isScopeActive('request')) {
            $this->request = $this->container->get('request');
        }
        $this->doctrine = $doctrine;
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
}
