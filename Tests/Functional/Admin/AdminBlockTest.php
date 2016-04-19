<?php

namespace alpixel\cmsbundle\Tests\Functional\Admin;

use Alpixel\Bundle\CMSBundle\Tests\Functional\BaseTestCase;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class AdminBlockTest extends BaseTestCase
{
    public function testConfiguresRoute()
    {
        $client = $this->createAuthentifiedClient();

        $client->request('GET', 'admin/block/list');
        $this->assertTrue($client->getResponse()->isSuccessful());

        $client->request('PUT', 'admin/block/1/edit');
//        $this->assertTrue($client->getResponse()->isSuccessful());

        var_dump($client->getResponse()->getStatusCode());
        $client->request('GET', 'admin/block/1/delete');
        var_dump($client->getResponse()->getStatusCode());
//        $this->assertTrue($client->getResponse()->isSuccessful());
//
//        $client->request('GET', 'admin/block/list');
//        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}