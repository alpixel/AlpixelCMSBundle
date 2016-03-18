<?php

namespace Alpixel\Bundle\CMSBundle\Helper;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Doctrine\ORM\EntityManager;

class CMSHelper
{
    protected $entityManager;
    protected $contentTypes;

    public function __construct(EntityManager $entityManager, $contentTypes)
    {
        $this->entityManager = $entityManager;
        $this->contentTypes = $contentTypes;
    }

    public function nodeGetTranslation(Node $node, $locale)
    {
        $node = $this->entityManager
                     ->getRepository('AlpixelCMSBundle:Node')
                     ->findTranslation($node, $locale);

        return $node;
    }

    public function createTranslation(Node $object, $locale)
    {
        if ($object->getTranslationSource() !== null) {
            $source = $object->getTranslationSource();
        } else {
            $source = $object;
        }

        $node = clone $object;
        $node->setLocale($locale);
        $node->setTranslationSource($source);
        $node->setTitle(sprintf('Version %s de la page "%s"', strtoupper($locale), $node->getTitle()));

        return $node;
    }

    public function getContentTypeFromNodeElementClass(Node $object)
    {
        foreach ($this->contentTypes as $contentType) {
            if ($contentType['class'] == get_class($object)) {
                return $contentType;
            }
        }
    }
}
