<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;

class BaseNodeEntityAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $container = $this->getConfigurationPool()->getContainer();

        $realLocales = [];
        if ($container->hasParameter('lunetics_locale.allowed_locales')) {
            $locales = $container->getParameter('lunetics_locale.allowed_locales');
            foreach ($locales as $val) {
                $realLocales[$val] = $val;
            }
        } else {
            $realLocales['fr'] = 'fr';
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
}
