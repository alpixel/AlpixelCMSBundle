<?php

namespace Alpixel\Bundle\CMSBundle\Entity;

abstract class AbstractNodeElement
{

    public function __clone()
    {
        $this->id = null;
        $this->node = clone $this->node;
    }
}
