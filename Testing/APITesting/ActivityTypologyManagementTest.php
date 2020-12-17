<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class ActivityTypologyManagementTest extends TestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = new Client([
            'base_uri' => 'http://api-team9.ddns.net',
            'timeout' => 2.0,
        ]);

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


    public function testInsertActivityTypologySuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['typologyid' => 'test1',
            'description' => 'test']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST','activitytypologies', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testInsertActivityTypologyNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['typologyid' => 'Test00',
            'description' => 'test' ]);
        $options = [
            'body'=>$body,
            'headers' => $headers,
            'http_errors' => false];
        $response = self::$client->request('POST','activitytypologies', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testInsertActivityTypologyNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('POST', 'activitytypologies',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testInsertActivityTypologyWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'Test00',
            'description' => 'test'  ]);
        $options = [
            'body'=>$body,
            'headers'=>$headers,
            'http_errors' => false];

        $response = self::$client->request('POST', 'activitytypologies', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }


    public function testGetActivityTypologiesSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activitytypologies', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $body = $body[0];
        if($body!=null) {
            $this->assertArrayHasKey('typologyid', $body);
            $this->assertArrayHasKey('description', $body);}

        $this->assertSame($code,200);
    }
    public function testGetActivityTypologiesNoDBLoader() : void {
        $headers = ["Authorization"=>$this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET','activitytypologies', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code,403);

    }
    public function testGetActivityTypologiesNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'activitytypologies',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }


    public function testGetActivityTypologySuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activitytypologies/test1', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('typologyid', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertSame($code,200);
    }
    public function testGetActivityTypologyDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activitytypologies/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetActivityTypologyNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'activitytypologies/test1',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetActivityTypologyWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activitytypologies/wrongID', $options);

        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }



    public function testModifyActivityTypologySuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['typologyid' => 'test1',
            'description' => 'test']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','activitytypologies/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testModifyActivityTypologyNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['typologyid' => 'test1',
            'description' => 'test' ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','activitytypologies/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testModifyActivityTypologyNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('PUT','activitytypologies/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testModifyActivityTypologyWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'test' ]);
        $options = [
            'body'=> $body,
            'headers'=> $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT', 'activitytypologies/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }



    public function testDeleteActivityTypologySuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','activitytypologies/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testDeleteActivityTypologyNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','activitytypologies/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testDeleteActivityTypologyNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('delete','activitytypologies/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testDeleteActivityTypologyWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','activitytypologies/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }

}
