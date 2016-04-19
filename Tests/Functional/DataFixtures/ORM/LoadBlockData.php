<?php

namespace alpixel\cmsbundle\Tests\Functional\DataFixtures\ORM;

use Alpixel\Bundle\CMSBundle\Entity\Block;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class LoadBlockData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $block = new Block();
        $block
            ->setContent('Some Lorem')
            ->setDateCreated(new \DateTime())
            ->setDateUpdated(new \DateTime())
            ->setLocale('fr');

        $manager->persist($block);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}