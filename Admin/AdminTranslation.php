<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class AdminTranslation extends AbstractAdmin
{
    protected $baseRouteName = 'alpixel_admin_cms_command_translation';
    protected $baseRoutePattern = 'command/translation';

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list']);
        $collection->add('command');
    }

    public static function getAvailableCommands()
    {
        return [[
            'name'      => 'alpixel:cms:translations:download',
            'arguments' => [
                'flush_cache' => true,
            ],
            'label'     => 'Importer les traductions',
            'icon'      => 'download',
        ]];
    }
}
