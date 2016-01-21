<?php

namespace Alpixel\Bundle\CMSBundle\Helper;

use Alpixel\Bundle\CMSBundle\Entity\NodeInterface;
use Doctrine\ORM\EntityManager;

class CMSHelper
{
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function nodeGetTranslation(NodeInterface $node, $locale)
    {
        $node = $this->entityManager
                     ->getRepository('CMSBundle:Node')
                     ->findTranslation($node, $locale);

        return $node;
    }
}
