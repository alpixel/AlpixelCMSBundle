<?php

namespace Alpixel\Bundle\CMSBundle\Entity;

interface NodeInterface
{
    public function defineNodeType();

    public function __clone();
}
