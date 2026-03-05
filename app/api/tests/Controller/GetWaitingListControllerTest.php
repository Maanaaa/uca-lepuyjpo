<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;

class GetWaitingListControllerTest extends TestCase
{
    public function testGetWaitingList(): void
    {
        // On tape sur ton serveur Docker (port 8080)
        $url = 'http://localhost/api/visites/waiting/3';
        
        $response = @file_get_contents($url);
        $status = $http_response_header[0] ?? 'HTTP/1.1 404 Not Found';

        // Ça doit échouer ici car la route n'existe pas encore
        $this->assertStringContainsString('200', $status, "L'API devrait répondre 200 OK");
        
        $data = json_decode($response, true);
        $this->assertIsArray($data);
    }
}