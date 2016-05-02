<?php

namespace Alpixel\Bundle\CMSBundle\Admin\Block;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\BlockBundle\Block\BaseBlockService;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class BlockHello extends BaseBlockService
{
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'AlpixelCMSBundle:admin/Block:block_hello.html.twig',
        ]);
    }
}
