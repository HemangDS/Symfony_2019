<?php

namespace iFlair\LetsBonusFrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase
{
    public function testTest()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/test');
    }
}
