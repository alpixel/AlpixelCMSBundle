<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AdminNode extends Admin
{

    protected $datagridValues   = array(
        '_page'       => 1,
        '_sort_order' => 'DESC',
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('editContent', $this->getRouterIdParameter() . '/edit/node');
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $cmsContentTypes = $this->getConfigurationPool()->getContainer()->getParameter('cms.content_types');
        $arrayField      = array();

        foreach ($cmsContentTypes as $key => $array) {
            $arrayField[$array['class']] = $array['title'];
        }

        $datagridMapper
            ->add('id')
            ->add('locale', null, array(
                'label' => 'Langue',
            ))
            ->add('title', null, array(
                'label' => 'Page',
            ))
            ->add('node', 'doctrine_orm_callback', array(
                'label'    => 'Type de contenu',
                'callback' => function($queryBuilder, $alias, $field, $value){
                    if(!$value['value']) {
                        return false;
                    }

                    $saveQueryBuilder = clone $queryBuilder;
                    $contentNode      = $saveQueryBuilder
                        ->select('n.id')
                        ->from($value['value'], 'c')
                        ->join('c.node', 'n')
                        ->getQuery()
                        ->getResult()
                    ;

                    $nodeIdTab = array();
                    foreach ($contentNode as $content) {
                        $nodeIdTab[] = $content['id'];
                    }

                    $queryBuilder
                        ->andWhere(sprintf('%s.id IN (:id)', $alias))
                        ->setParameter('id', $nodeIdTab)
                    ;

                    return true;
                }
            ),
                'choice',
                array(
                    'choices' => $arrayField,
                )
            )
        ;
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
            ->add('locale', null, array(
                'label' => 'Langue',
            ))
            ->add('title', null, array(
                'label' => 'Page',
            ))
            ->add('type', null, array(
                'label' => 'Type',
                'template' => 'CMSBundle:admin:fields/list__field_type.html.twig',
            ))
            ->add('dateCreated', null, array(
                'label' => 'Date de création',
            ))
            ->add('dateUpdated', null, array(
                'label' => 'Date d\'édition',
            ))
            ->add('published', null, array(
                'label' => 'Publié',
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'Voir' => array('template' => 'CMSBundle:admin:fields/list__action_see.html.twig'),
                    'editContent' => array('template' => 'CMSBundle:admin:fields/list__action_edit.html.twig'),
                    'delete' => array(),
                ),
            ))
        ;
    }
}
