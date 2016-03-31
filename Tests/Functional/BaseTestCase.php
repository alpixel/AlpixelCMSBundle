<?php


namespace Alpixel\Bundle\CMSBundle\Tests\Functional;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;


/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class BaseTestCase extends WebTestCase
{
    static protected function createKernel(array $options = array())
    {
        return new AppKernel(
            isset($options['config']) ? $options['config'] : 'default.yml'
        );
    }
}