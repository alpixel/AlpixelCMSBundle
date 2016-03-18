<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BaseNodeEntityAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $realLocales = [];
        $locales = $container->getParameter('lunetics_locale.allowed_locales');
        foreach ($locales as $val) {
            $realLocales[$val] = $val;
        }

        $formMapper
            ->add('title', null, [
                'label'    => 'Titre',
                'required' => true,
            ])
            ->add('content', 'ckeditor', [
                'label'       => 'Contenu',
                'required'    => true,
                'config_name' => 'admin',
            ])
            ->add('locale', 'choice', [
                'label'    => 'Langue du contenu',
                'choices'  => $realLocales,
                'required' => true,
            ])
            ->add('published', 'checkbox', [
                'label'    => 'Publié',
                'required' => false,
            ]);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'ID',
            ])
            ->add('title', null, [
                'label' => 'Titre',
            ])
            ->add('published', null, [
                'label' => 'Publié',
            ])
            ->add('dateCreated', null, [
                'label' => 'Crée',
            ])
            ->add('dateUpdated', null, [
                'label' => 'Mis à jour',
            ])
            ->add('_action', 'actions');
    }
}
