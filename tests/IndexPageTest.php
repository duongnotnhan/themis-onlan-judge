<?php

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

require_once __DIR__ . '/../vendor/autoload.php';

class IndexPageTest extends TestCase
{
    private $client;
    private $baseUrl;

    protected function setUp(): void
    {
        $this->client = new Client([
            'http_errors' => false,
            'timeout' => 5.0,
        ]);

        $this->baseUrl = 'http://localhost';
    }

    public function testIndexPageLoadsSuccessfully()
    {
        $response = $this->client->get($this->baseUrl . '/index.php');

        $this->assertEquals(200, $response->getStatusCode());

        $body = (string) $response->getBody();
        $this->assertStringContainsString('<title>', $body, '<index> tag not found!');
    }
}
