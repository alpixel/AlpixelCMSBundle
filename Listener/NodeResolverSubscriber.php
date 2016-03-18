<?php

namespace Alpixel\Bundle\CMSBundle\Listener;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Events;

class NodeResolverSubscriber implements EventSubscriber
{
    private $contentTypes;

    public function __construct($contentTypes = [])
    {
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
        if (empty($this->productInheritance)) {
            return;
        }

        $metadata = $eventArgs->getClassMetadata();
        if (Node::CLASS !== $metadata->getName()) {
            return;
        }

        $discriminatorMap = [];
        foreach ($this->contentTypes as $key=>$contentType) {
            $discriminatorMap[$key] = $contentType['class'];
        }

        if (!empty($discriminatorMap)) {
            $metadata->setDiscriminatorMap($discriminatorMap);
        }
    }
}
