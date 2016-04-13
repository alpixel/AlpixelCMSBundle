<?php

namespace Alpixel\Bundle\CMSBundle\Twig\Extension;

class TextExtension extends \Twig_Extension
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'alpixel_text_extension';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('get_class', [$this, 'getClass']),
        ];
    }

    public function getClass($object)
    {
        return get_class($object);
    }
}
