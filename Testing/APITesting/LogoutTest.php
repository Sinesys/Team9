<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class LogoutTest extends TestCase{
    private $client;
    private $token_login;

    protected function setUp(): void{

        $this->client = new Client([
            'base_uri' => 'http://api-team9.ddns.net',
            'timeout' => 2.0,
        ]);

        $body = json_encode(['id' => 'admin2', 'password' => 'admin']);
        $options = [
            'body' => $body,
            'http_errors' => false,
            'delay'=>1000
        ];

        $response = $this->client->request('POST', 'login', $options);

        $body = json_decode($response->getBody(), true);

        $this->token_login =$body['auth_token'];
    }

    public function testLogoutSuccessful(): void
    {
        $headers = ["Authorization"=>$this->token_login];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = $this->client->request('POST', 'logout',  $options);

        $code = $response->getStatusCode();
        $this->assertSame($code, 200);

    }

    public function testLogoutWrongToken(): void
    {
        $headers = ["Authorization"=>'wrongtoken'];

        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];

        $response = $this->client->request('POST', 'logout',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }

    public function testLogoutNoHeader(): void
    {
        $headers = [''];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];

        $response = $this->client->request('POST', 'logout',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }

}
