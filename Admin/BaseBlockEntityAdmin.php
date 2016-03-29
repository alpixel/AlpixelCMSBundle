<?php


namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;


/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class BaseBlockEntityAdmin extends Admin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('edit', 'delete'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $realLocales = [];
        if ($container->hasParameter('lunetics_locale.allowed_locales')) {
            $locales = $container->getParameter('lunetics_locale.allowed_locales');
            foreach ($locales as $val) {
                $realLocales[$val] = $val;
            }
        } else {
            $realLocales['fr'] = 'fr';
        }

        $formMapper
            ->add('content', 'ckeditor', [
                'label' => 'Contenu',
                'required' => true,
                'config_name' => 'admin',
            ])
            ->end();
    }
}