<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class BaseNodeEntityAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $formMapper
            ->add('node.title', null, [
                'label'    => 'Titre',
                'required' => true,
            ])
            ->add('node.content', 'ckeditor', [
                'label'       => 'Contenu',
                'required'    => true,
                'config_name' => 'admin',
            ])
            ->add('node.locale', 'choice', [
                'label'    => 'Langue du contenu',
                'choices'  => $container->getParameter('lunetics_locale.allowed_locales'),
                'required' => true,
            ])
            ->add('node.published', 'checkbox', [
                'label'    => 'Publié',
                'required' => false,
            ]);
    }
}
