<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testIndexPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/product');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');

        // try with sort parameter to ensure query works
        $client->request('GET', '/product?sort=price_asc');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('h1');
    }
}
