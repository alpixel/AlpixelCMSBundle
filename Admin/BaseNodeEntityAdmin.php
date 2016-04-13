<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Alpixel\Bundle\CMSBundle\Form\DateTimeSingleType;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

abstract class BaseNodeEntityAdmin extends BaseAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['add', 'edit', 'delete']);
        $collection->add('see', $this->getRouterIdParameter().'/see');
        $collection->add('createTranslation', $this->getRouterIdParameter().'/translate');
    }

    protected function configureMainFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, [
                'label'    => 'Titre',
                'required' => true,
            ])
            ->add('content', 'ckeditor', [
                'label'       => 'Contenu',
                'required'    => false,
                'config_name' => 'admin',
            ]);
    }

    public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {
        if (isset($this->breadcrumbs[$action])) {
            return $this->breadcrumbs[$action];
        }

        $contentTypes = $this->getCMSTypes();
        $menu = $this->menuFactory->createItem('root');

        $menu = $menu->addChild('Dashboard',
            ['uri' => $this->routeGenerator->generate('sonata_admin_dashboard')]
        );

        $menu = $menu->addChild('Gestion des pages',
            ['uri' => $this->routeGenerator->generate('alpixel_admin_cms_node_list')]
        );

        $menu = $menu->addChild(sprintf('Gestion des pages de type "%s"', $contentTypes[$this->getSubject()->getType()]['title']),
            ['uri' => $this->routeGenerator->generate('alpixel_admin_cms_node_list')]
        );

        return $this->breadcrumbs[$action] = $menu;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        self::configureMainFields($formMapper);

        $formMapper
            ->add('locale', 'choice', [
                'label'    => 'Langue du contenu',
                'choices'  => $this->getRealLocales(),
                'required' => true,
            ])
            ->add('dateCreated', DateTimeSingleType::class, [
                'label' => 'Date de création',
            ])
            ->add('published', 'checkbox', [
                'label'    => 'Publié',
                'required' => false,
            ]);
    }

    public function showCustomURL($object = null)
    {
    }
}
