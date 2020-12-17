<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class MaintainerTest extends TestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client([
            'base_uri' => 'http://api-team9.ddns.net',
            'timeout' => 2.0,]);

    }

    private function login($id, $password)
    {
        $body = json_encode(['id' => $id, 'password' => $password]);
        $options = [
            'body' => $body,
            'http_errors' => false,
            'delay'=>1000,
        ];

        $response = self::$client->request('POST', 'login', $options);
        $body = json_decode($response->getBody(), true);
        return $body['auth_token'];

    }


    public function testGetMaintainersSuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','maintainers', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $body = $body[0];
        if($body!=null) {
            $this->assertArrayHasKey('userid', $body);
            $this->assertArrayHasKey('name', $body);
            $this->assertArrayHasKey('surname', $body);
            $this->assertArrayHasKey('email', $body);
            $this->assertArrayHasKey('phonenumber', $body);
            $this->assertArrayHasKey('birthdate', $body);
            $this->assertArrayHasKey('competences', $body);
            $this->assertArrayHasKey('unavailability', $body);
        }


        $this->assertSame($code,200);
    }
    public function testGetMaintainersNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','maintainers', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetMaintainersNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'maintainers',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }


    public function testGetMaintainerSuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','maintainers/maintainer2', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('userid', $body);
        $this->assertArrayHasKey('name', $body);
        $this->assertArrayHasKey('surname', $body);
        $this->assertArrayHasKey('email', $body);
        $this->assertArrayHasKey('phonenumber', $body);
        $this->assertArrayHasKey('birthdate', $body);
        $this->assertArrayHasKey('competences', $body);
        $this->assertArrayHasKey('unavailability', $body);

        $this->assertSame($code,200);
    }
    public function testGetMaintainerNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','maintainers/maintainer2', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetMaintainerNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'maintainers/maintainer2',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetMaintainerWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','maintainers/maintainer104', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testGetMaintainersFromToSuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','maintainers?availfromday=2020-12-25&availtoday=2020-12-31&availfromhour=08:00&availtohour=17:00', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $body = $body[0];
        if($body!=null) {
            $this->assertArrayHasKey('userid', $body);
            $this->assertArrayHasKey('name', $body);
            $this->assertArrayHasKey('surname', $body);
            $this->assertArrayHasKey('email', $body);
            $this->assertArrayHasKey('phonenumber', $body);
            $this->assertArrayHasKey('birthdate', $body);
            $this->assertArrayHasKey('competences', $body);
            $this->assertArrayHasKey('unavailability', $body);
            if($body['unavailability']!=null)
                $this->assertIsArray($body['unavailability'], 'Assert unavailability is array');}

        $this->assertSame($code,200);
    }
    public function testGetMaintainersFromToNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','maintainers?availfromday=2020-12-25&availtoday=2020-12-31&availfromhour=08:00&availtohour=17:00', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetMaintainersFromToNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'maintainers?availfromday=2020-12-25&availtoday=2020-12-31&availfromhour=08:00&availtohour=17:00',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }

}