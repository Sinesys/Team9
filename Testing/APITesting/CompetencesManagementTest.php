<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class CompetencesManagementTest extends TestCase
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



    public function testInsertCompetenceSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['competenceid' => 'test1',
            'name' => 'test']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST','competences', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testInsertCompetenceNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['competenceid' => 'Test00',
            'name' => 'test' ]);
        $options = [
            'body'=>$body,
            'headers' => $headers,
            'http_errors' => false];
        $response = self::$client->request('POST','competences', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testInsertCompetenceNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('POST', 'competences',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testInsertCompetenceWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'Test00',
            'name' => 'test'  ]);
        $options = [
            'body'=>$body,
            'headers'=>$headers,
            'http_errors' => false];

        $response = self::$client->request('POST', 'competences', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }



    public function testGetCompetencesSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
        'headers' => $headers,
        'http_errors' => false];

        $response = self::$client->request('GET','competences', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $body = $body[0];
        if($body!=null){
            $this->assertArrayHasKey('competenceid', $body);
            $this->assertArrayHasKey('name', $body);
        }

        $this->assertSame($code,200);
}
    public function testGetCompetencesNoAdmin() : void {
        $headers = ["Authorization"=>$this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET','competences', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code,403);

    }
    public function testGetCompetencesNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'competences',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }


    public function testGetCompetenceSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','competences/test1', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('competenceid', $body);
        $this->assertArrayHasKey('name', $body);
        $this->assertSame($code,200);
    }
    public function testGetCompetenceNoAdmin() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','competences/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetCompetenceNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'competences/test1',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetCompetenceWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','competences/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testModifyCompetenceSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['competenceid' => 'test1',
            'name' => 'test']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','competences/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testModifyCompetenceNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['competenceid' => 'test1',
            'name' => 'test' ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','competences/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testModifyCompetenceNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('PUT','competences/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testModifyCompetenceWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'test' ]);
        $options = [
            'body'=> $body,
            'headers'=> $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT', 'competences/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testDeleteCompetenceSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','competences/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testDeleteCompetenceNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','competences/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testDeleteCompetenceNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('delete','competences/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testDeleteCompetenceWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','competences/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }

}
