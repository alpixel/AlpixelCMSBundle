<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Doctrine\DBAL\Query\QueryBuilder;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminNode extends BaseAdmin
{
    protected $baseRouteName = 'alpixel_admin_cms_node';
    protected $baseRoutePattern = 'node';
    protected $classnameLabel = 'pages';

    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'dateUpdated',
    ];

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'batch', 'delete']);
        $collection->add('forwardEdit');
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $entityManager = $container->get('doctrine.orm.default_entity_manager');
        $datagridMapper
            ->add('locale', 'doctrine_orm_callback', [
                'label' => 'Langue',
                'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                    if (!$value['value']) {
                        return false;
                    }
                    $queryBuilder
                        ->andWhere($alias . '.locale = :locale')
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
                'label' => 'Type de contenu',
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
                        ->andWhere($alias . '.id IN (:ids)')
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
                'label' => 'Type',
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
                    'see' => ['template' => 'AlpixelCMSBundle:admin:fields/list__action_see.html.twig'],
                    'edit' => ['template' => 'AlpixelCMSBundle:admin:fields/list__action_edit.html.twig'],
                    'delete' => ['template' => 'AlpixelCMSBundle:admin:fields/list__action_delete.html.twig'],
                ],
            ]);
    }

    public function buildBreadcrumbs($action, MenuItemInterface $menu = null)
    {
        if (isset($this->breadcrumbs[$action])) {
            return $this->breadcrumbs[$action];
        }

        $menu = $this->menuFactory->createItem('root');

        $menu = $menu->addChild('Dashboard',
            ['uri' => $this->routeGenerator->generate('sonata_admin_dashboard')]
        );

        $menu = $menu->addChild('Gestion des pages',
            ['uri' => $this->routeGenerator->generate('alpixel_admin_cms_node_list')]
        );

        return $this->breadcrumbs[$action] = $menu;
    }

    public function createQuery($context = 'list')
    {
        $container = $this->getConfigurationPool()->getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');

        $query = parent::createQuery($context);

        if ($this->isGranted('ROLE_SONATA_ADMIN') === false) {
            $contentTypes = $this->getCMSTypes();

            $viewableCMS = [];
            foreach ($contentTypes as $key => $contentType) {
                try {
                    if (isset($contentType['admin'])) {
                        $contentType['admin']->checkAccess("list"); //Throw an exception if doesn' have access
                        $viewableCMS[$key] = $contentType;
                    }
                } catch (AccessDeniedException $e) {

                }
            }

            $queryBuilder = clone $query;
            /** @var QueryBuilder $queryBuilder */

            $orX = $queryBuilder->expr()->orX();
            $orX->add($queryBuilder->expr()->eq('2', '1'));

            foreach ($viewableCMS as $key => $viewableContent) {
                $nodes = $entityManager->getRepository($viewableContent['class'])->findAll();
                $nodesId = [];

                foreach($nodes as $node) {
                    $nodesId[] = $node->getId();
                }

                $orX->add($queryBuilder->expr()->in($queryBuilder->getRootAlias() . '.id', $nodesId));
            }
            $queryBuilder->andWhere($orX);
            return $queryBuilder;
        }

        return $query;
    }

}
