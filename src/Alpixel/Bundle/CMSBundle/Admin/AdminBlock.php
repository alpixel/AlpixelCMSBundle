<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AdminBlock extends Admin
{
    protected $datagridValues   = array(
        '_page'       => 1,
        '_sort_order' => 'DESC',
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('editContent', $this->getRouterIdParameter() . '/edit/block');
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
            ->add('name', null, array(
                'label' => 'Nom',
            ))
            ->add('dateCreated', null, array(
                'label' => 'Date de création',
            ))
            ->add('dateUpdated', null, array(
                'label' => 'Date d\'édition',
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'editContent' => array('template' => 'CMSBundle:admin:fields/list__action_edit.html.twig'),
                    'delete' => array(),
                ),
            ))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Home')
                ->add('name', null, array(
                    'label' => 'Nom',
                ))
                ->add('content', 'ckeditor', array(
                    'config_name' => 'admin',
                ))
                ->add('dateCreated', null, array(
                    'label' => 'Date de création',
                    'years' => range(date('Y', strtotime('- 100 years')), date('Y', strtotime('+ 10 years'))),
                ))
                ->add('dateUpdated', null, array(
                    'label' => 'Date d\'édition',
                    'years' => range(date('Y', strtotime('- 100 years')), date('Y', strtotime('+ 10 years'))),
                ))
            ->end()
        ;
    }

}