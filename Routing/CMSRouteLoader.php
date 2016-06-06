<?php

namespace Alpixel\Bundle\CMSBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class CMSRouteLoader extends Loader
{
    private $loaded = false;

    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "alpixel_cms" loader twice');
        }

        $collection = new RouteCollection();

        $resource = '@AlpixelCMSBundle/Resources/config/routing.yml';
        $type = 'yaml';

        $importedRoutes = $this->import($resource, $type);
        $collection->addCollection($importedRoutes);

        $this->loaded = true;

        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return 'alpixel_cms' === $type;
    }
}
