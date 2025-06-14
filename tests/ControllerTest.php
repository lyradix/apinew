<?php

namespace App\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Response;

class PageControllerTest extends WebTestCase;
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful(expectStatusCode: Response::HTTP_OK);
    }

}


namespace App\Tests\Controller;