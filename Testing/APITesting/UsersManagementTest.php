<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class UsersManagementTest extends TestCase
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


    public function testInsertUserSuccesful() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $body = json_encode(['userid' => 'Test00',
            'password' => 'test',
            'name' => 'Test',
            'surname' => 'Test',
            'phonenumber'=>'33333333333',
            'email' => 'test@gmail.com',
            'birthdate'=> '1999-02-19',
            'role' => 'PLN' ]);

        $options = [
            'body' => $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST','users', $options);

        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testInsertUserNoAdmin() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $body = json_encode(['userid' => 'Test00',
            'password' => 'test',
            'name' => 'Test',
            'surname' => 'Test',
            'email' => 'test@gmail.com',
            'phonenumber'=>'33333333333',
            'birthdate'=> '1999-02-19',
            'role' => 'PLN' ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];
        $response = self::$client->request('POST','users', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testInsertUserNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('POST', 'users',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testInsertUserWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('admin2','admin')];

        $body = json_encode(['wrongfield' => 'Test00',
            'password' => 'test',
            'name' => 'Test',
            'surname' => 'Test',
            'email' => 'test@gmail.com',
            'birthdate'=> '1999-02-19',
            'phonenumber'=>'33333333333',
            'role' => 'PLN' ]);
        $options = [
            'body'=>$body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST', 'users', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }


    public function testGetUsersSuccesful() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','users', $options);
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
            $this->assertArrayHasKey('role', $body);
            if ($body['role'] == 'MNT') {
                $this->assertArrayHasKey('competences', $body);
            }
        }
        $this->assertSame($code,200);
    }
    public function testGetUsersNoAdmin() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','users', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetUsersNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'users',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }


    public function testGetUserSuccesful() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','users/admin2', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('userid', $body);
        $this->assertArrayHasKey('name', $body);
        $this->assertArrayHasKey('surname', $body);
        $this->assertArrayHasKey('email', $body);
        $this->assertArrayHasKey('phonenumber', $body);
        $this->assertArrayHasKey('birthdate', $body);
        $this->assertArrayHasKey('role', $body);
        if($body['role']=='MNT'){
            $this->assertArrayHasKey('competences', $body);
        }
        $this->assertSame($code,200);
    }
    public function testGetUserNoAdmin() : void {
        $headers = ["Authorization"=> $this->login('maintainer2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','users/admin2', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 403);
    }
    public function testGetUserNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'users/admin2',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetUserWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','users/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }
    

    public function testModifyUserSuccesful() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $body = json_encode(['name' => 'Test',
            'surname' => 'Test',
            'email' => 'test@gmail.com',
            'phonenumber'=>'33333333333',
            'password'=>'',
            'birthdate'=> '1999-02-19'
            ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','users/Test00', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testModifyUserNoAdmin() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $body = json_encode(['name' => 'Test',
            'surname' => 'Test',
            'email' => 'test@gmail.com',
            'phonenumber'=>'33333333333',
            'birthdate'=> '19/02/1999']);
        $options = [
            'body', $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','users/Test00', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testModifyUserNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('PUT','users/Test00', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testModifyUserWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('admin2','admin')];

        $body = json_encode(['wrongfield' => 'Test',
            'surname' => 'Test',
            'email' => 'test@gmail.com',
            'phonenumber'=>'33333333333',
            'birthdate'=> '19/02/1999']);
        $options = [
            'body'=> $body,
            'headers'=>$headers,
            'http_errors' => false];

        $response = self::$client->request('PUT', 'users/Test00', $options);

        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }


    public function testDeleteUserSuccesful() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','users/Test00', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testDeleteUserNoAdmin() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','users/Test00', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testDeleteUserNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('delete','users/Test00', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testDeleteUserWrongID(): void
    {

        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','users/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }






}
