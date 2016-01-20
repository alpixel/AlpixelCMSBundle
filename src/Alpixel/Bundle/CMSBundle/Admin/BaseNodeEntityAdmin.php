<?php

namespace Alpixel\Bundle\CMSBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BaseNodeEntityAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $container       = $this->getConfigurationPool()->getContainer();

        $formMapper
            ->add('node.title', null, array(
                'label'    => 'Titre',
                'required' => true,
            ))
            ->add('node.content', 'ckeditor', array(
                'label'       => 'Contenu',
                'required'    => true,
                'config_name' => 'admin',
            ))
            ->add('node.locale', 'choice', array(
                'label'    => 'Langue du contenu',
                'choices'  => $container->getParameter('lunetics_locale.allowed_locales'),
                'required' => true,
            ))
            ->add('node.published', 'checkbox', array(
                'label'    => 'Publié',
                'required' => false,
            ))
        ;
    }
}
