<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AdminBlock extends BaseBlockEntityAdmin
{
    protected $baseRouteName = 'alpixel_admin_cms_block';
    protected $baseRoutePattern = 'block';

    protected $datagridValues = [
        '_page'       => 1,
        '_sort_order' => 'DESC',
        '_sort_by'    => 'dateUpdated',
    ];

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'edit']);
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('locale', null, [
                'label' => 'Langue',
            ])
            ->add('name', null, [
                'label' => 'Nom',
            ])
            ->add('dateCreated', null, [
                'label' => 'Date de création',
            ])
            ->add('dateUpdated', null, [
                'label' => 'Date d\'édition',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => ['template' => 'AlpixelCMSBundle:admin:fields/list__block_action_edit.html.twig'],
                ],
            ]);
    }
}
