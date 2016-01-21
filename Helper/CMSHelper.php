<?php

namespace Alpixel\Bundle\CMSBundle\Helper;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Alpixel\Bundle\CMSBundle\Entity\NodeInterface;
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

    public function nodeGetTranslation(NodeInterface $node, $locale)
    {
        $node = $this->entityManager
                     ->getRepository('CMSBundle:Node')
                     ->findTranslation($node->getNode(), $locale);

        return $node;
    }

    public function createTranslation(Node $object, $locale)
    {
        if ($object->getTranslationSource() !== null) {
            $source = $object->getTranslationSource();
        } else {
            $source = $object;
        }

        $content = $this->getNodeElementEntityFromNode($object);
        $translatedContent = clone $content;

        $node = $translatedContent->getNode();
        $node->setLocale($locale);
        $node->setTranslationSource($source);
        $node->setTitle(sprintf('Version %s de la page "%s"', strtoupper($locale), $node->getTitle()));

        return $translatedContent;
    }

    public function getNodeElementEntityFromNode(Node $node)
    {
        if (array_key_exists($node->getType(), $this->contentTypes)) {
            $contentType = $this->contentTypes[$node->getType()];

            return $this->entityManager
                        ->getRepository($contentType['class'])
                        ->findOneByNode($node);
        }
    }

    public function getContentTypeFromNodeElementClass(NodeInterface $object)
    {
        foreach ($this->contentTypes as $contentType) {
            if ($contentType['class'] == get_class($object)) {
                return $contentType;
            }
        }
    }
}
