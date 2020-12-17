<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class SiteManagementTest extends TestCase
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


    public function testInsertSiteSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['siteid' => 'test1',
            'area' => 'test',
            'department'=>'test']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST','sites', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testInsertSiteNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['siteid' => 'Test00',
            'area' => 'test',
            'department'=>'test' ]);
        $options = [
            'body'=>$body,
            'headers' => $headers,
            'http_errors' => false];
        $response = self::$client->request('POST','sites', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testInsertSiteNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('POST', 'sites',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testInsertSiteWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'Test00',
            'area' => 'test',
            'department'=>'test'  ]);
        $options = [
            'body'=>$body,
            'headers'=>$headers,
            'http_errors' => false];

        $response = self::$client->request('POST', 'sites', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }



    public function testGetSitesSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','sites', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $body = $body[0];
        if($body!=null) {
            $this->assertArrayHasKey('siteid', $body);
            $this->assertArrayHasKey('area', $body);
            $this->assertArrayHasKey('department', $body);
        }
        $this->assertSame($code,200);
    }
    public function testGetSitesNoAdmin() : void {
        $headers = ["Authorization"=>$this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET','sites', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code,403);

    }
    public function testGetSitesNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'sites',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }



    public function testGetSiteSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','sites/test1', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('siteid', $body);
        $this->assertArrayHasKey('area', $body);
        $this->assertArrayHasKey('department', $body);
        $this->assertSame($code,200);
    }
    public function testGetSiteNoAdmin() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','sites/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetSiteNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'sites/test1',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetSiteWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','sites/wrongID', $options);

        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testModifySiteSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['siteid' => 'test1',
            'area' => 'test',
            'department'=>'test']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','sites/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testModifySiteNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['siteid' => 'test1',
            'area' => 'test',
            'department'=>'test' ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','sites/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testModifySiteNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('PUT','sites/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testModifySiteWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'test' ]);
        $options = [
            'body'=> $body,
            'headers'=> $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT', 'sites/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }



    public function testDeleteSiteSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','sites/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testDeleteSiteNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','sites/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testDeleteSiteNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('delete','sites/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testDeleteSiteWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','sites/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }

}

