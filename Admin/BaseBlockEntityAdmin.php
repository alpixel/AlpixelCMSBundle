<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class BaseBlockEntityAdmin extends BaseAdmin
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
        $collection->clearExcept(['edit']);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('content',  CKEditorType::class, [
                'label'       => 'Contenu',
                'required'    => true,
                'config_name' => 'admin',
            ])
            ->end();
    }
}
