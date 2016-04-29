<?php

namespace Alpixel\Bundle\CMSBundle\Admin\Block;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sonata\BlockBundle\Block\BaseBlockService;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class BlockTimeline extends BaseBlockService
{
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'template' => 'AlpixelCMSBundle:admin/Block:block_timeline.html.twig',
        ]);
    }
}
