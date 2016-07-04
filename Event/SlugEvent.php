<?php

namespace Alpixel\Bundle\CMSBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 *
 * The SlugEvent class is used for an entity which have a slug
 * shared between different entities.
 */
class SlugEvent extends Event
{
    private $entity;
    private $property;

    /**
     * SlugEvent constructor.
     *
     * @param $entity object
     * @param $property string
     */
    public function __construct($entity, $property)
    {
        if (!is_object($entity)) {
            throw new \InvalidArgumentException('The "$entity" parameter must be an object.');
        } elseif (!empty($property) && is_string($property)) {
            throw new \InvalidArgumentException('The "$property" parameter must be a non empty string.');
        }

        $this->entity = $entity;
        $this->property = $property;
    }

    /**
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }
}
