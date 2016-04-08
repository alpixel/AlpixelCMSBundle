<?php

namespace Alpixel\Bundle\CMSBundle\Tests\Functional;

/**
 * @author Benjamin HUBERT <benjamin@alpixel.fr>
 */
class AdminTest extends BaseTestCase
{
    public function testTranschoiceWhenTranslationNotYetExtracted()
    {
        $client = $this->createClient();
        $client->request('GET', '/admin/dashboard?_locale=en');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), substr($response, 0, 2000));
    }
}
