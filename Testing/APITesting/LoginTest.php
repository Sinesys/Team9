<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class LoginTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {

        $this->client = new Client([
            'base_uri' => 'http://api-team9.ddns.net',
            'timeout' => 2.0,
        ]);
    }

    public function testLoginSuccessful(): void
    {

        $body = json_encode(['id' => 'admin1', 'password' => 'admin']);
        $options = [
            'body' => $body,
            'http_errors' => false
        ];

        $response = $this->client->request('POST', 'login', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('role', $body);
        $this->assertArrayHasKey('auth_token', $body);
        $this->assertSame($code, 200);
    }

    public function testLoginWrongCredentials(): void
    {
        $body = json_encode(['id' => 'admin1', 'password' => 'wrongpassword']);
        $options = [
            'body' => $body,
            'http_errors' => false
        ];

        $response = $this->client->request('POST', 'login', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertSame($code, 403);
    }

    public function testLoginWrongJSONFormat(): void
    {

        $body = json_encode(['id' => 'admin1', 'wrongfield' => 'wrongpassword']);
        $options = [
            'body' => $body,
            'http_errors' => false
        ];

        $response = $this->client->request('POST', 'login', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertSame($code, 400);
    }

}