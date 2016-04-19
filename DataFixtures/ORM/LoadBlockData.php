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
            ->setName('Some Lorem Name')
            ->setContent('Some Lorem Content')
            ->setDateCreated(new \DateTime('-6 month'))
            ->setDateUpdated(new \DateTime())
            ->setLocale('fr')
            ->setSlug('some-slug');

        $manager->persist($block);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}