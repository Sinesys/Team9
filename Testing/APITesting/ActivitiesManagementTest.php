<?php

require 'vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

final class ActivitiesManagementTest extends TestCase
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


    public function testInsertActivitySuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $body = json_encode(['activityid' => 'test1',
            'description' => 'test',
            'scheduledweek' => 30,
            'estimatedtime' => 45,
            'site'=>"site1",
            'typology'=>"typology1",
            "procedure"=> "procedure1",
            'interruptible'=> true,
            'materials'=>[]
            ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST','activities', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testInsertActivityNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $body = json_encode(['activityid' => 'test00',
            'description' => 'test',
            'scheduledweek' => 30,
            'estimatedtime' => 45,
            'site'=>'site1',
            'typology'=>'typology1',
            "procedure"=> "procedure1",
            'interruptible'=> true,
            'materials'=>[]
        ]);
        $options = [
            'body'=>$body,
            'headers' => $headers,
            'http_errors' => false];
        $response = self::$client->request('POST','activities', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testInsertActivityNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('POST', 'activities',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testInsertActivityWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('planner2','admin')];

        $body = json_encode(['wrongfield' => 'test1',
            'description' => 'test',
            'scheduledweek' => 30,
            'estimatedtime' => 45,
            'site'=>'site1',
            'typology'=>'typology1',
            "procedure"=> "procedure1",
            'interruptible'=> true,
            'materials'=>[]
        ]);
        $options = [
            'body'=>$body,
            'headers'=>$headers,
            'http_errors' => false];

        $response = self::$client->request('POST', 'activities', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }


    public function testGetActivitiesSuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $body = $body[0];
        if($body!=null) {
            $this->assertArrayHasKey('activityid', $body);
            $this->assertArrayHasKey('description', $body);
            $this->assertArrayHasKey('scheduledweek', $body);
            $this->assertArrayHasKey('assignedto', $body);
            $this->assertArrayHasKey('estimatedtime', $body);
            $this->assertArrayHasKey('site', $body);
            $this->assertArrayHasKey('typology', $body);
            $this->assertArrayHasKey('procedure', $body);
            $this->assertArrayHasKey('interruptible', $body);
            $this->assertArrayHasKey('materials', $body);
            if($body['assignedto']!=null)
                $this->assertIsArray($body['assignedto'], 'Assert assignedto is array');

        }
        $this->assertSame($code,200);
    }
    public function testGetActivitiesNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetActivitiesNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'activities',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }


    public function testGetActivitiesVerboseSuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities?verbose=true', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);

        $body = $body[0];
        if($body!=null) {
            $this->assertArrayHasKey('activityid', $body);
            $this->assertArrayHasKey('description', $body);
            $this->assertArrayHasKey('scheduledweek', $body);
            $this->assertArrayHasKey('assignedto', $body);
            $this->assertArrayHasKey('estimatedtime', $body);
            $this->assertArrayHasKey('interruptible', $body);
            $this->assertArrayHasKey('materials', $body);
            $this->assertArrayHasKey('site', $body);
            $this->assertArrayHasKey('typology', $body);
            $this->assertArrayHasKey('procedure', $body);
            if($body['materials']!=null)
                $this->assertIsArray($body['materials'], 'Assert materials is array');

            if($body['assignedto']!=null)
                $this->assertIsArray($body['assignedto'], 'Assert assignedto is array');
        }

        $this->assertSame($code,200);
    }
    public function testGetActivitiesVerboseNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities?verbose=true', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetActivitiesVerboseNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'activities?verbose=true',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }

    public function testGetActivityVerboseSuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities/test1?verbose=true', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $this->assertSame($code,200);

        $this->assertArrayHasKey('activityid', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('scheduledweek', $body);
        $this->assertArrayHasKey('estimatedtime', $body);
        $this->assertArrayHasKey('site', $body);
        $this->assertArrayHasKey('typology', $body);
        $this->assertArrayHasKey('procedure', $body);
        $this->assertArrayHasKey('interruptible', $body);
        $this->assertArrayHasKey('materials', $body);
        $this->assertArrayHasKey('assignedto', $body);
        if($body['materials']!=null)
            $this->assertIsArray($body['materials'], 'Assert materials is array');

        if($body['assignedto']!=null)
            $this->assertIsArray($body['assignedto'], 'Assert assignedto is array');


    }
    public function testGetActivityVerboseNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities/test1?verbose=true', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetActivityVerboseNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'activities/test1?verbose=true',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetActivityVerboseWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities/wrongID?verbose=true', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testGetActivitySuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities/test1', $options);
        $code = $response->getStatusCode();
        $body = json_decode($response->getBody(), true);
        $this->assertSame($code,200);

        $this->assertArrayHasKey('activityid', $body);
        $this->assertArrayHasKey('description', $body);
        $this->assertArrayHasKey('scheduledweek', $body);
        $this->assertArrayHasKey('estimatedtime', $body);
        $this->assertArrayHasKey('site', $body);
        $this->assertArrayHasKey('typology', $body);
        $this->assertArrayHasKey('procedure', $body);
        $this->assertArrayHasKey('interruptible', $body);
        $this->assertArrayHasKey('materials', $body);
        $this->assertArrayHasKey('assignedto', $body);

        if($body['assignedto']!=null)
            $this->assertIsArray($body['assignedto'], 'Assert assignedto is array');


    }
    public function testGetActivityNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testGetActivityNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('GET', 'activities/test1',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testGetActivityWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('GET','activities/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testModifyActivitySuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $body = json_encode([
            'description' => 'update',
            'scheduledweek' => 30,
            'estimatedtime' => 45,
            'site'=>'site1',
            'typology'=>'typology1',
            "procedure"=> "procedure1",
            'interruptible'=> true,
            'materials'=>[]
        ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','activities/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testModifyActivityNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $body = json_encode(['activityid' => 'test1',
            'description' => 'test',
            'scheduledweek' => 30,
            'estimatedtime' => 45,
            'site'=>'site1',
            'typology'=>'typology1',
            "procedure"=> "procedure1",
            'interruptible'=> true,
            'materials'=>[]
        ]);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT','activities/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testModifyActivityNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('PUT','activities/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testModifyActivityWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('planner2','admin')];

        $body = json_encode([
            'wrongfield' => 'test',
            'scheduledweek' => 30,
            'estimatedtime' => 45,
            'site'=>'site1',
            'typology'=>'typology1',
            "procedure"=> "procedure1",
            'interruptible'=> true,
            'materials'=>[]
        ]);
        $options = [
            'body'=> $body,
            'headers'=> $headers,
            'http_errors' => false];

        $response = self::$client->request('PUT', 'activities/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testInsertActivityAssignmentSuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $body = json_encode([
            'userid' => 'maintainer1',
            'day' => '2020-12-31',
            'starttime' => '10:30',
            'endtime'=>'17:30']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST','assignactivity/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testInsertActivityAssignmentNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $body = json_encode([
            'userid' => 'maintainer1',
            'day' => '2020-12-31',
            'starttime' => '10:30',
            'endtime'=>'17:30']);
        $options = [
            'body'=> $body,
            'headers' => $headers,
            'http_errors' => false];
        $response = self::$client->request('POST','assignactivity/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testInsertActivityAssignmentNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('POST', 'assignactivity/test1',   $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testInsertActivityAssignmentWrongJSONFormat(): void
    {
        $headers = ["Authorization"=> $this->login('planner2','admin')];

        $body = json_encode([
            'wrongfield' => 'maintainer1',
            'day' => '2020-12-31',
            'starttime' => '10:30',
            'endtime'=>'17:30']);
        $options = [
            'body'=> $body,
            'headers'=>$headers,
            'http_errors' => false];

        $response = self::$client->request('POST', 'assignactivity/test1', $options);
        $code = $response->getStatusCode();

        $this->assertSame($code, 400);
    }
    public function testInsertActivityAssignmentWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $body = json_encode([
            'userid' => 'maintainer1',
            'day' => '2020-12-31',
            'starttime' => '10:30',
            'endtime'=>'17:30']);
        $options = [
            'body'=>$body,
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('POST','assignactivity/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testDeleteActivityAssignmentSuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','assignactivity/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testDeleteActivityAssignmentNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','assignactivity/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testDeleteActivityAssignmentNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('delete','assignactivity/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testDeleteActivityAssignmentWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','assignactivity/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }


    public function testDeleteActivitySuccesful() : void {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','activities/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,200);
    }
    public function testDeleteActivityNoPlanner() : void {
        $headers = ["Authorization"=> $this->login('admin2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','activities/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code,403);
    }
    public function testDeleteActivityNoHeader() : void {
        $headers = [''];
        $options =  [
            'headers' => $headers,
            'http_errors' => false
        ];
        $response = self::$client->request('delete','activities/test1', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 401);
    }
    public function testDeleteActivityWrongID(): void
    {
        $headers = ["Authorization"=> $this->login('planner2','admin')];
        $options = [
            'headers' => $headers,
            'http_errors' => false];

        $response = self::$client->request('delete','activities/wrongID', $options);
        $code = $response->getStatusCode();
        $this->assertSame($code, 400);
    }

}