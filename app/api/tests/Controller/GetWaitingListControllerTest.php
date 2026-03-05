<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;

class GetWaitingListControllerTest extends TestCase
{
    public function testGetWaitingListStructure(): void
    {
        $url = 'http://localhost:8080/api/visites/waiting/3';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode, "L'API doit répondre 200 OK");
        
        $content = json_decode($response, true);
        $this->assertIsArray($content, "La réponse doit être un tableau JSON");
    }
}