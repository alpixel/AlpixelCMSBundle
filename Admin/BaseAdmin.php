<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;

class BaseAdmin extends Admin
{
    protected $realLocales;
    protected $cmsTypes;
    protected $cmsEntityTypes;

    protected function getRealLocales()
    {
        if (!empty($this->realLocales)) {
            return $this->realLocales;
        }

        $container = $this->getConfigurationPool()->getContainer();
        $realLocales = [];
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

    protected function getCMSTypes()
    {
        if (!empty($this->cmsTypes)) {
            return $this->cmsTypes;
        }

        $container       = $this->getConfigurationPool()->getContainer();
        $types           = $container->getParameter('alpixel_cms.content_types');
        $this->cmsTypes  = [];
        foreach ($types as $key => $array) {
            $this->cmsTypes[$array['class']] = $array['title'];
        }

        return $this->cmsTypes;
    }

    protected function getCMSEntityTypes()
    {
        if (!empty($this->cmsEntityTypes)) {
            return $this->cmsEntityTypes;
        }

        $cmsTypes             = $this->getCMSTypes();
        $this->cmsEntityTypes = [];
        foreach ($cmsTypes as $class => $title) {
            $type = lcfirst(substr($class, (strrpos($class, '\\') + 1) , strlen($class)));
            $this->cmsEntityTypes[$type] = $title;
        }

        return $this->cmsEntityTypes;
    }
}
