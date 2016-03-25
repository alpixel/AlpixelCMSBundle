<?php

namespace Alpixel\Bundle\CMSBundle\Twig\Extension;

use Alpixel\Bundle\CMSBundle\Entity\Block;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpFoundation\RequestStack;

class BlockExtension extends \Twig_Extension
{
    protected $doctrine;
    protected $blocks;
    protected $container;
    protected $request;

    public function __construct($container, RequestStack $requestStack, Registry $doctrine, $blocks = null)
    {
        $this->container = $container;
        $this->request = $requestStack->getCurrentRequest();
        $this->doctrine = $doctrine;
        $this->blocks = $blocks;
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
        if ($this->blocks === null) {
            return;
        }

        if (array_key_exists($blockName, $this->blocks)) {
            $blockConf = $this->blocks[$blockName];

            $block = $this
                ->doctrine
                ->getManager()
                ->getRepository('AlpixelCMSBundle:Block')
                ->findOneBy(
                    ['slug' => $blockName],
                    []
                );

            if ($block === null) {
                $block = new Block();
                $block->setName($blockConf['title']);
                $block->setContent($blockConf['default']);
                $block->setSlug($blockName);

                if (!empty($blockConf['class']) && $blockConf['class'] !== null && $blockConf['class'] != "Alpixel\Bundle\CMSBundle\Entity\Block") {
                    $subBlock = new $blockConf['class']();
                    $subBlock->setBlock($block);
                    $this->doctrine->getManager()->persist($subBlock);
                }

                $this->doctrine->getManager()->persist($block);
                $this->doctrine->getManager()->flush();
            }

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
}
