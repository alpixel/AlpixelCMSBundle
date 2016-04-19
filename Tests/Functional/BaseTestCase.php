<?php

namespace Alpixel\Bundle\CMSBundle\Tests\Functional;

use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class BaseTestCase extends WebTestCase
{
    protected function logIn($client)
    {
        /** @var Container $container */
        $container = $client->getContainer();
        $session = $container->get('session');

        $token = new UsernamePasswordToken('jean josé', null, 'admin', ['ROLE_SUPER_ADMIN']);
        $container->get('security.token_storage')->setToken($token);
        $container->get('session')->set('_security_main', serialize($token));
    }

    protected static function createKernel(array $options = [])
    {
        return new AppKernel(
            isset($options['config']) ? $options['config'] : 'config.yml'
        );
    }
}
