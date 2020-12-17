<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class AccessLogTest extends TestCase{
    private static $client;

    public static function setUpBeforeClass(): void{
        self::$client=new Client([
            'base_uri' => 'http://api-team9.ddns.net',
            'timeout' => 2.0, ]);}
    private function login($id, $password) {
        $body = json_encode(['id' => $id, 'password' => $password]);
        $options = [
            'body' => $body,
            'http_errors' => false,];

        $response = self::$client->request('POST', 'login', $options);
        $body = json_decode($response->getBody(), true);
        return $body['auth_token'];}



    public function testAccessLogSuccessful () : void {
        $headers = ["Authorization"=>$this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET','accesslog', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }

    public function testAccessLogNoAdmin () : void {
        $headers = ["Authorization"=>$this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET','accesslog', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code,403);
    }

    public function testAccessLogNoHeader():void{
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'accesslog',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }



}