<?php

namespace Alpixel\Bundle\CMSBundle\Listener;

use Alpixel\Bundle\CMSBundle\Entity\Block;
use Alpixel\Bundle\CMSBundle\Entity\Node;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class CMSResolverSubscriber implements EventSubscriber
{
    private $contentTypes;
    private $blocks;

    public function __construct($contentTypes = [], $blocks = [])
    {
        $this->blocks = $blocks;
        $this->contentTypes = $contentTypes;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::loadClassMetadata,
        ];
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        $discriminatorMap = [];
        if (Node::class === $metadata->getName()) {
            foreach ($this->contentTypes as $key => $contentType) {
                $discriminatorMap[$key] = $contentType['class'];
            }
        } elseif (Block::class === $metadata->getName()) {
            foreach ($this->blocks as $key => $block) {
                $discriminatorMap[$key] = $block['class'];
            }
        } else {
            return;
        }

        if (!empty($discriminatorMap)) {
            $metadata->setDiscriminatorMap($discriminatorMap);
        }
    }
}
