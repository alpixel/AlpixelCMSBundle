<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class AdminNode extends Admin
{
    protected $baseRouteName = 'cms_node';
    protected $baseRoutePattern = 'cms';

    protected $datagridValues = [
        '_page'       => 1,
        '_sort_order' => 'DESC',
    ];

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('editContent', $this->getRouterIdParameter().'/edit/node');
        $collection->add('createTranslation', $this->getRouterIdParameter().'/translate');
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $cmsContentTypes = $this->getConfigurationPool()->getContainer()->getParameter('cms.content_types');
        $arrayField = [];

        foreach ($cmsContentTypes as $key => $array) {
            $arrayField[$array['class']] = $array['title'];
        }

        $datagridMapper
            ->add('id')
            ->add('locale', null, [
                'label' => 'Langue',
            ])
            ->add('title', null, [
                'label' => 'Page',
            ])
            ->add('node', 'doctrine_orm_callback', [
                'label'    => 'Type de contenu',
                'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }

                    $saveQueryBuilder = clone $queryBuilder;
                    $contentNode = $saveQueryBuilder
                        ->select('n.id')
                        ->from($value['value'], 'c')
                        ->join('c.node', 'n')
                        ->getQuery()
                        ->getResult();

                    $nodeIdTab = [];
                    foreach ($contentNode as $content) {
                        $nodeIdTab[] = $content['id'];
                    }

                    $queryBuilder
                        ->andWhere(sprintf('%s.id IN (:id)', $alias))
                        ->setParameter('id', $nodeIdTab);

                    return true;
                },
            ],
                'choice',
                [
                    'choices' => $arrayField,
                ]
            );
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
            ->add('title', null, [
                'label' => 'Page',
            ])
            ->add('type', null, [
                'label'    => 'Type',
                'template' => 'CMSBundle:admin:fields/list__field_type.html.twig',
            ])
            ->add('dateCreated', null, [
                'label' => 'Date de création',
            ])
            ->add('dateUpdated', null, [
                'label' => 'Date d\'édition',
            ])
            ->add('published', null, [
                'label' => 'Publié',
            ])
            ->add('_action', 'actions', [
                'actions' => [
                    'Voir'        => ['template' => 'CMSBundle:admin:fields/list__action_see.html.twig'],
                    'editContent' => ['template' => 'CMSBundle:admin:fields/list__action_edit.html.twig'],
                    'delete'      => [],
                ],
            ]);
    }
}
