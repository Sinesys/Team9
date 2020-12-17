<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class ProcedureManagementTest extends TestCase
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


    public function testInsertProcedureSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['procedureid' => 'test1',
            'description' => 'test',
            'competencesrequired'=>[]]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST','procedures', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testInsertProcedureNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['procedureid' => 'Test00',
            'description' => 'test',
            'competencesrequired'=>[] ]);
        $options = [
            'body'=>$body,
            'headers' => $headers,
            'http_errors' => false];
        $response = self::$client->request('POST','procedures', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testInsertProcedureNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('POST', 'procedures',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testInsertProcedureWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'Test00',
            'description' => 'test',
            'competencesrequired'=>[]  ]);
        $options = [
            'body'=>$body,
            'headers'=>$headers,
            'http_errors' => false];

        $response = self::$client->request('POST', 'procedures', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }



    public function testGetProceduresSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','procedures', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $body = $body[0];
        if($body!=null) {
            $this->assertArrayHasKey('procedureid', $body);
            $this->assertArrayHasKey('description', $body);
            $this->assertArrayHasKey('competencesrequired', $body);

        }
        $this->assertSame($code,200);
    }
    public function testGetProceduresNoAdmin() : void {
        $headers = ["Authorization"=>$this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET','procedures', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code,403);

    }
    public function testGetProceduresNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'procedures',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }


    public function testGetProceduresVerboseSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','procedures?verbose=true ', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $body = $body[0];
        if($body!=null) {
            $this->assertArrayHasKey('procedureid', $body);
            $this->assertArrayHasKey('description', $body);
            $this->assertArrayHasKey('competencesrequired', $body);
        }
        if($body['competencesrequired']!=null)
            $this->assertIsArray($body['competencesrequired'], 'Assert competencesrequired is array');

        $this->assertSame($code,200);
    }
    public function testGetProceduresVerboseNoAdmin() : void {
        $headers = ["Authorization"=>$this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET','procedures?verbose=true ', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code,403);

    }
    public function testGetProceduresVerboseNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'procedures?verbose=true ',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }


    public function testGetProcedureSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','procedures/test1', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertSame($code,200);
        $this->assertArrayHasKey('procedureid', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('competencesrequired', $body);

    }
    public function testGetProcedureNoAdmin() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','procedures/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetProcedureNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'procedures/test1',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetProcedureWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','procedures/wrongID', $options);

        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testModifyProcedureSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body = json_encode(['procedureid' => 'test1',
            'description' => 'test',
            'competencesrequired'=>[]]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','procedures/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testModifyProcedureNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body = json_encode(['procedureid' => 'test1',
            'description' => 'test',
            'competencesrequired'=>[] ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','procedures/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testModifyProcedureNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('PUT','procedures/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testModifyProcedureWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];

        $body = json_encode(['wrongfield' => 'test' ]);
        $options = [
            'body'=> $body,
            'headers'=> $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT', 'procedures/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }



    public function testInsertSMPSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $formData = [['name' =>"file", 'contents'=>fopen(__DIR__ . '/test.pdf', 'r')]];
        $options = [
            'headers' => $headers,
            'http_errors' => false,
            'multipart' =>$formData];

        $response = self::$client->request('POST','procedures/test1/SMP', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testInsertSMPNoPermission() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $body  = ['file' =>"test.ext"];
        $options = [
            'form_params'=>$body,
            'headers' => $headers,
            'http_errors' => false];
        $response = self::$client->request('POST','procedures/test1/SMP', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testInsertSMPNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('POST', 'procedures/test1/SMP',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testInsertSMPWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $body  = ['wrong' =>"test.ext"];
        $options = [
            'form_params'=>$body,
            'headers'=>$headers,
            'http_errors' => false];

        $response = self::$client->request('POST', 'procedures/test1/SMP', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }


    public function testGetSMPSuccessful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','procedures/test1/SMP', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code,200);
    }
    public function testGetSMPNoPermission() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','procedures/test1/SMP', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetSMPNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'procedures/test1/SMP',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetSMPWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','procedures/wrongID/SMP', $options);

        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testDeleteSMPSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','procedures/test1/SMP', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testDeleteSMPNoPermission() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','procedures/test1/SMP', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testDeleteSMPNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('delete','procedures/test1/SMP', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testDeleteSMPWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','procedures/wrongID/SMP', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }



    public function testDeleteProcedureSuccesful() : void {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','procedures/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testDeleteProcedureNoDBLoader() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','procedures/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testDeleteProcedureNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('delete','procedures/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testDeleteProcedureWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('dbl2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','procedures/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }

}