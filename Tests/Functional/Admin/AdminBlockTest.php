<?php

namespace alpixel\cmsbundle\Tests\Functional\Admin;

use Alpixel\Bundle\CMSBundle\Tests\Functional\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class AdminBlockTest extends BaseTestCase
{
    public function __construct()
    {
        parent::__construct();
        $this->logIn($this->getUser('ROLE_SUPER_ADMIN'), new Response());
    }
//
//    public function testConfiguresRoute()
//    {
        // SUCCESS
//        $this->client->request('GET', 'admin/block/list');
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//
//        $crawler = $this->client->request('GET', 'admin/block/1/edit');
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//
//        $this->client->request('GET', 'admin/block/create');
//        $this->assertTrue($this->client->getResponse()->isNotFound());
//
//        $this->client->request('GET', 'admin/block/batch');
//        $this->assertTrue($this->client->getResponse()->isNotFound());
//
//        $this->client->request('GET', 'admin/block/export');
//        $this->assertTrue($this->client->getResponse()->isNotFound());
//
//        $this->client->request('GET', 'admin/block/1/delete');
//        $this->assertTrue($this->client->getResponse()->isNotFound());
//
//        $this->client->request('GET', 'admin/block/1/show');
//        $this->assertTrue($this->client->getResponse()->isNotFound());
//    }

    public function testConfigureListFields()
    {

        $crawler = $this->client->request('GET', 'admin/block/1/edit');
//        var_dump($this->client->getResponse()->getContent());
        $this->assertTrue($this->client->getResponse()->isSuccessful());

//        var_dump($client->getResponse()->getContent());die;
//        $this->assertContains('Gestion des blocs', $client->getResponse()->getContent());
//        $this->assertCount(1, $crawler->filter('div.form-group'));
//        $editButton = $crawler->selectButton('btn_update_and_edit');
//        $contentField = $crawler->filter('textarea')->extract(['name']);
//        $form = $editButton->form([
//            $contentField[0] => 'LOLOLO',
//        ]);
//        $form = $editButton->form();
//        $this->client->submit($form);
//        var_dump($this->client->getResponse()->getContent());
//        var_dump(self::$client->getContainer()->get('security.token_storage')->getToken());
//        var_dump($this->container->get('security.token_storage')->getToken());
//        var_dump($client->getResponse()->getContent());
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/admin/login');
//        $buttonCrawlerNode = $crawler->selectButton('_submit');
//        $form = $buttonCrawlerNode->form(array('_username' => 'alpixel','_password' => 'alpixel'));
//        $client->submit($form);
//        var_dump($client->getResponse()->getContent());
//        $crawler = $client->followRedirect();
//        $this->assertEquals(302, $client->getResponse()->getStatusCode());
//        $crawler = $client->request('GET', 'admin/block/1/edit');
//
    }
}