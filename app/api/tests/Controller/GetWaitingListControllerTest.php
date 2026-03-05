<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GetWaitingListControllerTest extends WebTestCase
{
    public function testGetWaitingListStructure(): void
    {
        $client = static::createClient(['environment' => 'dev']);
        
        $client->request('GET', '/api/visites/waiting/3');

        $this->assertResponseIsSuccessful();
        
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($content);
    }
}