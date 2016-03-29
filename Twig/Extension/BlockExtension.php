<?php

namespace Alpixel\Bundle\CMSBundle\Twig\Extension;

use Alpixel\Bundle\CMSBundle\Entity\Block;
use Alpixel\Bundle\CMSBundle\Helper\BlockHelper;

class BlockExtension extends \Twig_Extension
{
    private $blockHelper;
    private $container;
    private $blockConfiguration;

    public function __construct(BlockHelper $blockHelper, $container, $blockConfiguration)
    {
        $this->blockHelper = $blockHelper;
        $this->container = $container;
        $this->blockConfiguration = $blockConfiguration;
    }

    public function getName()
    {
        return 'cms_block';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cms_block', [$this, 'displayBlock'], [
                'is_safe'           => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function displayBlock(\Twig_Environment $twig, $blockName)
    {
        $block = $this->blockHelper->loadBlock($blockName);

        if ($block === null) {
            return;
        }

        $blockConf = $this->blockConfiguration[$blockName];
        if (!empty($blockConf['service'])) {
            $controller = $this->container->get($blockConf['service']);

            return $controller->renderAction();
        } else {
            $template = $blockConf['template'];
            if ($template === null) {
                $template = 'AlpixelCMSBundle:front:blocks/base_block.html.twig';
            }

            return $twig->render($template, [
                'block' => $block,
            ]);
        }
    }
}
