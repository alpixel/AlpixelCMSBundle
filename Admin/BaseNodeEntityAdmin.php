<?php

namespace Alpixel\Bundle\CMSBundle\Admin;

use Alpixel\Bundle\CMSBundle\Form\DateTimeSingleType;
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
            if (!$container->hasParameter('default_locale')) {
                throw new \LogicException('You must set the default_locale parameter');
            }
            $locale = $container->getParameter('default_locale');
            $realLocales[$locale] = $locale;
        }

        $formMapper
            ->add('title', null, [
                'label'    => 'Titre',
                'required' => true,
            ])
            ->add('content', 'ckeditor', [
                'label'       => 'Contenu',
                'required'    => false,
                'config_name' => 'admin',
            ])
            ->add('locale', 'choice', [
                'label'    => 'Langue du contenu',
                'choices'  => $realLocales,
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
}
