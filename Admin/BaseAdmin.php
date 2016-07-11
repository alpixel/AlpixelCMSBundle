<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;

abstract class BaseAdmin extends AbstractAdmin
{
    protected $realLocales;
    protected $cmsTypes;
    protected $blockTypes;
    protected $cmsEntityTypes;

    protected function getRealLocales()
    {
        if (!empty($this->realLocales)) {
            return $this->realLocales;
        }

        $container = $this->getConfigurationPool()->getContainer();

        if ($container->hasParameter('lunetics_locale.allowed_locales')) {
            $locales = $container->getParameter('lunetics_locale.allowed_locales');
            foreach ($locales as $val) {
                $this->realLocales[$val] = $val;
            }
        } else {
            $locale = $container->getParameter('default_locale');
            $this->realLocales[$locale] = $locale;
        }

        return $this->realLocales;
    }

    public function getBlockTypes()
    {
        if (!empty($this->blockTypes)) {
            return $this->blockTypes;
        }

        $container = $this->getConfigurationPool()->getContainer();
        $types = $container->getParameter('alpixel_cms.blocks');
        $this->blockTypes = $types;
        foreach ($this->blockTypes as $key => $array) {
            if ($instanceAdmin = $this->getConfigurationPool()->getAdminByClass($array['class'])) {
                $this->blockTypes[$key]['admin'] = $instanceAdmin;
            }
        }

        return $this->blockTypes;
    }

    public function getCMSTypes()
    {
        if (!empty($this->cmsTypes)) {
            return $this->cmsTypes;
        }

        $container = $this->getConfigurationPool()->getContainer();
        $types = $container->getParameter('alpixel_cms.content_types');
        $this->cmsTypes = $types;
        foreach ($this->cmsTypes as $key => $array) {
            if ($instanceAdmin = $this->getConfigurationPool()->getAdminByClass($array['class'])) {
                $this->cmsTypes[$key]['admin'] = $instanceAdmin;
            }
        }

        return $this->cmsTypes;
    }

    protected function getCMSEntityTypes()
    {
        if (!empty($this->cmsEntityTypes)) {
            return $this->cmsEntityTypes;
        }

        $cmsTypes = $this->getCMSTypes();
        $this->cmsEntityTypes = [];
        foreach ($cmsTypes as $key => $array) {
            $type = lcfirst(substr($array['class'], (strrpos($array['class'], '\\') + 1), strlen($array['class'])));
            $this->cmsEntityTypes[$type] = $array['title'];
        }

        return $this->cmsEntityTypes;
    }

    public function setContentTypes($contentTypes)
    {
        $this->cmsTypes = $contentTypes;
    }

    public function getContentTypes()
    {
        return $this->cmsTypes;
    }
}
