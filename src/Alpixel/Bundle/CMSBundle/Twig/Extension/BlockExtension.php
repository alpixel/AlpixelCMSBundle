<?php

namespace Alpixel\Bundle\CMSBundle\Twig\Extension;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Alpixel\Bundle\CMSBundle\Entity\Block;

class BlockExtension extends \Twig_Extension
{
    protected $doctrine;
    protected $blocks;
    protected $container;
    protected $request;

    public function __construct($container, Registry $doctrine, $blocks)
    {
        $this->container = $container;

        if ($this->container->isScopeActive('request')) {
            $this->request = $this->container->get('request');
        }
        $this->doctrine     = $doctrine;
        $this->blocks       = $blocks;
    }

    public function getName()
    {
        return 'cms_block';
    }

    public function getFunctions()
    {
        return array(
            'cms_block'     => new \Twig_Function_Method($this, 'displayBlock', array(
                'is_safe' => array('html'),
                'needs_environment' => true
            ))
        );
    }

    public function displayBlock(\Twig_Environment $twig, $blockName)
    {
        if(array_key_exists($blockName, $this->blocks)) {
            $blockConf = $this->blocks[$blockName];

            $block = $this
                ->doctrine
                ->getManager()
                ->getRepository('CMSBundle:Block')
                ->findOneBy(
                    array('slug' => $blockName),
                    array()
                );

            if($block === null) {
                $block = new Block;
                $block->setName($blockConf['title']);
                $block->setContent($blockConf['default']);
                $block->setSlug($blockName);

                if(!empty($blockConf['class']) && $blockConf['class'] !== null && $blockConf['class'] != "Alpixel\Bundle\CMSBundle\Entity\Block") {
                    $subBlock = new $blockConf['class'];
                    $subBlock->setBlock($block);
                    $this->doctrine->getManager()->persist($subBlock);
                }

                $this->doctrine->getManager()->persist($block);
                $this->doctrine->getManager()->flush();
            }

            if(!empty($blockConf['service'])) {
                $controller = $this->container->get($blockConf['service']);
                return $controller->renderAction();
            } else {
                return $twig->render($blockConf['template'], array(
                    'block' => $block,
                ));
            }

        }
    }

}
