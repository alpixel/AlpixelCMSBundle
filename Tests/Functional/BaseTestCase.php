<?php

namespace Alpixel\Bundle\CMSBundle\Tests\Functional;

use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Model\User;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\BrowserKit\CookieJar;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class BaseTestCase extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;
    /**
     * @var null|\Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var MockFileSessionStorage
     */
    protected $storage;
    /**
     * @var Session
     */
    protected $session;


    public function __construct()
    {
        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->storage = new MockFileSessionStorage(__DIR__ . '/cache/sessions');
        $this->session = new Session($this->storage);
    }

    public function getUser($role = null)
    {
        if (!isset($this->user)) {
            $user = $this->getUserManager()->findUserByUsername('alpixel');

            if (isset($user)) {
                $this->user = $user;
            } else {
                $this->user = $this->getUserManager()->createUser();

                $this->user->setEnabled(true);
                $this->user->setFirstname('alpixel');
                $this->user->setLastname('alpixel');
                $this->user->setUsername('alpixel');
                $this->user->setEmail('benjamin@alpixel.fr');
                $this->user->setPlainPassword('alpixel');
                $this->getUserManager()->updatePassword($this->user);
                if (isset($role)) {
                    $this->user->addRole($role);
                }
                $this->getUserManager()->updateUser($this->user);
            }
        }

        return $this->user;
    }

    public function logIn(User $user, Response $response)
    {
        $this->session->start();

        $firewallName = 'admin';

        $this->cookie = new Cookie($this->storage->getName(), $this->storage->getId());
        $this->cookieJar = new CookieJar();
        $this->cookieJar->set($this->cookie);

        $this->token = new UsernamePasswordToken($user, 'alpixel', $firewallName, $user->getRoles());
        $this->session->set('_security_' . $firewallName, serialize($this->token));

        $this->getSecurityManager()->loginUser($firewallName,
            $user,
            $response
        );

        $this->session->save();
    }

    protected static function createKernel(array $options = [])
    {
        return new AppKernel(
            isset($options['config']) ? $options['config'] : 'config.yml'
        );
    }


    public function getUserManager()
    {
        return $this->container->get('fos_user.user_manager');

    }

    public function getSecurityManager()
    {
        return $this->container->get('fos_user.security.login_manager');

    }

}
