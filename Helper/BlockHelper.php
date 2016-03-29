<?php

namespace Alpixel\Bundle\CMSBundle\Helper;

use Alpixel\Bundle\CMSBundle\Entity\Block;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class BlockHelper
{
    protected $entityManager;
    protected $blocks;
    protected $request;

    public function __construct(RequestStack $requestStack, EntityManager $entityManager, $blocks)
    {
        $this->request = $requestStack->getMasterRequest();
        $this->entityManager = $entityManager;
        $this->blocks = $blocks;
    }

    /**
     * @param $blockType
     * @param null $locale
     *
     * @return Block|null
     */
    public function loadBlock($blockType, $locale = null)
    {
        if (!isset($this->blocks[$blockType])) {
            return;
        }

        if ($locale === null) {
            $locale = $this->request->getLocale();
        }

        $block = $this
            ->entityManager
            ->getRepository('AlpixelCMSBundle:Block')
            ->findBlock($blockType, $locale);

        if ($block === null) {
            $blockConf = $this->blocks[$blockType];

            $block = new $blockConf['class']();
            $block->setName($blockConf['title']);

            foreach ($blockConf['default'] as $key => $value) {
                $block->{$key} = $value;
            }

            $block->setLocale($locale);
            $block->setSlug($blockType);

            $this->entityManager->persist($block);
            $this->entityManager->flush();
        }

        return $block;
    }

    public function getContentTypeFromNodeElementClass(Block $object)
    {
        foreach ($this->blocks as $block) {
            if ($block['class'] == get_class($object)) {
                return $block;
            }
        }
    }
}
