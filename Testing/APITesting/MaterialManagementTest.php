<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class MaterialManagementTest extends TestCase
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



    public function testInsertMaterialSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['materialid' => 'test1',
            'name' => 'test']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST','materials', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testInsertMaterialNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['materialid' => 'Test00',
            'name' => 'test' ]);
        $options = [
            'body'=>$body,
            'headers' => $headers,
            'http_errors' => false];
        $response = self::$client->request('POST','materials', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testInsertMaterialNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('POST', 'materials',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testInsertMaterialWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'Test00',
            'name' => 'test'  ]);
        $options = [
            'body'=>$body,
            'headers'=>$headers,
            'http_errors' => false];

        $response = self::$client->request('POST', 'materials', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }



    public function testGetMaterialsSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','materials', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $body = $body[0];
        if($body!=null){
        $this->assertArrayHasKey('materialid', $body);
        $this->assertArrayHasKey('name', $body);}

        $this->assertSame($code,200);
    }
    public function testGetMaterialsNoAdmin() : void {
        $headers = ["Authorization"=>$this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET','materials', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code,403);

    }
    public function testGetMaterialsNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'materials',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }


    public function testGetMaterialSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','materials/test1', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('materialid', $body);
        $this->assertArrayHasKey('name', $body);
        $this->assertSame($code,200);
    }
    public function testGetMaterialNoAdmin() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','materials/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetMaterialNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'materials/test1',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetMaterialWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','materials/wrongID', $options);

        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }



    public function testModifyMaterialSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['materialid' => 'test1',
            'name' => 'test']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','materials/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testModifyMaterialNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['materialid' => 'test1',
            'name' => 'test' ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','materials/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testModifyMaterialNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('PUT','materials/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testModifyMaterialWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'test' ]);
        $options = [
            'body'=> $body,
            'headers'=> $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT', 'materials/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }



    public function testDeleteMaterialSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','materials/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testDeleteMaterialNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','materials/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testDeleteMaterialNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('delete','materials/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testDeleteMaterialWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','materials/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }

}

