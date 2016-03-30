<?php

namespace Alpixel\Bundle\CMSBundle\Twig\Extension;

use Alpixel\Bundle\CMSBundle\Entity\Block;
use Alpixel\Bundle\CMSBundle\Helper\BlockHelper;

class BlockExtension extends \Twig_Extension
{
    private $blockHelper;
    private $container;
    private $blockConfiguration;

    /**
     * BlockExtension constructor.
     * @param BlockHelper $blockHelper
     * @param $container
     * @param $blockConfiguration
     */
    public function __construct(BlockHelper $blockHelper, $container, $blockConfiguration)
    {
        $this->blockHelper = $blockHelper;
        $this->container = $container;
        $this->blockConfiguration = $blockConfiguration;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'cms_block';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cms_block', [$this, 'displayBlock'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    /**
     * @param \Twig_Environment $twig
     * @param $blockName
     * @return string|void
     */
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
            if (!isset($blockConf['template'])) {
                $template = 'AlpixelCMSBundle:front:blocks/base_block.html.twig';
            } else {
                $template = $blockConf['template'];
            }

            return $twig->render($template, [
                'block' => $block,
            ]);
        }
    }
}
