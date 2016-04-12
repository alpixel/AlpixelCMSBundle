<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Alpixel\Bundle\CMSBundle\Form\DateTimeSingleType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class BaseNodeEntityAdmin extends BaseAdmin
{
    protected $baseRouteName = 'alpixel_admin_cms_node';
    protected $baseRoutePattern = 'node';
    protected $datagridValues = [
        '_page'       => 1,
        '_sort_order' => 'DESC',
        '_sort_by'    => 'dateUpdated',
    ];

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('editContent', $this->getRouterIdParameter().'/edit/node');
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
                'label' => 'Contenu',
                'required' => false,
                'config_name' => 'admin',
            ]);
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
                'label'    => 'Date de création',
            ])
            ->add('published', 'checkbox', [
                'label'    => 'Publié',
                'required' => false,
            ]);
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('locale', null, [
                'label' => 'Langue',
            ])
            ->add('title', null, [
                'label' => 'Page',
            ])
            ->add('type', null, [
                'label'    => 'Type',
                'template' => 'AlpixelCMSBundle:admin:fields/list__field_type.html.twig',
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
                    'Voir'        => ['template' => 'AlpixelCMSBundle:admin:fields/list__action_see.html.twig'],
                    'editContent' => ['template' => 'AlpixelCMSBundle:admin:fields/list__action_edit.html.twig'],
                    'delete'      => [],
                ],
            ]);
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $container     = $this->getConfigurationPool()->getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');

        $datagridMapper
            ->add('locale', 'doctrine_orm_callback', [
                'label'   => 'Langue',
                'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }

                    $queryBuilder
                        ->andWhere($alias.'.locale = :locale')
                        ->setParameter('locale', $value['value']);

                    return true;
                },
            ],
            'choice',
            [
                'choices' => $this->getRealLocales(),
            ])
            ->add('title', null, [
                'label' => 'Page',
            ])
            ->add('published', null, [
                'label' => 'Publié',
            ])
            ->add('node', 'doctrine_orm_callback', [
                'label'    => 'Type de contenu',
                'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) use ($entityManager) {
                    if (!$value['value']) {
                        return false;
                    }

                    // We can't query the type from the AlpixelCMSBundle:Node repository (InheritanceType) because of that
                    // we try to get the repository in AppBundle with the value which is the class name of entity. :pig:
                    try {
                        $repository = $entityManager->getRepository(sprintf('AppBundle:%s', ucfirst($value['value'])));
                    } catch (\Doctrine\Common\Persistence\Mapping\MappingException $e) {
                        return false;
                    }

                    $data = $repository->findAll();
                    if (empty($data)) {
                        return false;
                    }

                    $queryBuilder
                        ->andWhere($alias.'.id IN (:ids)')
                        ->setParameter('ids', $data);

                    return true;
                },
            ],
                'choice',
                [
                    'choices' => $this->getCMSEntityTypes(),
                ]
            );
    }
}
