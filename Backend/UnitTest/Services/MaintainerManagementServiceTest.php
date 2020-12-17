<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/maintainerManagementService.php');

class MaintainerManagementServiceTest extends TestCase{
    private $service;

    protected function setUp() : void{
        $this->service = new  MaintainerManagementService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');}

    public function testGetMaintainersSuccesful(){
        $result = $this->service->getMaintainersInfo();
        $this->assertIsArray($result, 'Assert is array');
        foreach ($result as $r){
            $this->assertArrayHasKey('userid',$r, 'Array has key userid');
            $this->assertArrayHasKey('competences',$r, 'Array has key competences');
            $this->assertArrayHasKey('name', $r, 'Array has key name');
            $this->assertArrayHasKey('surname', $r, 'Array has key surname');
            $this->assertArrayHasKey('email',$r, 'Array has key email');
            $this->assertArrayHasKey('phonenumber',$r, 'Array has key phonenumber');
            $this->assertArrayHasKey('birthdate',$r, 'Array has key birthdate');
            $this->assertArrayHasKey('unavailability',$r, 'Array has key unavailability');
        }}

    public function testGetMaintainerSuccesful(){
        $r = $this->service->getMaintainerInfo('MANT_TEST');
        $this->assertArrayHasKey('userid',$r, 'Array has key userid');
        $this->assertArrayHasKey('competences',$r, 'Array has key competences');
        $this->assertArrayHasKey('name', $r, 'Array has key name');
        $this->assertArrayHasKey('surname', $r, 'Array has key surname');
        $this->assertArrayHasKey('email',$r, 'Array has key email');
        $this->assertArrayHasKey('phonenumber',$r, 'Array has key phonenumber');
        $this->assertArrayHasKey('birthdate',$r, 'Array has key birthdate');
        $this->assertArrayHasKey('unavailability',$r, 'Array has key unavailability');}

    public function testGetMaintainerWrongID(){
        $this->expectException(WrongDataException::class);
        $this->service->getMaintainerInfo('wrongID');}

    public function testGetMaintainerWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->service->getMaintainerInfo('');}

    public function testGetMaintainersUnavailRangeSuccesful(){
        $result = $this->service->getMaintainersUnavailabilitiesRange('2021-01-01', '2021-01-02','10:00', '14:50');
        $this->assertIsArray($result, 'Assert is array');
        foreach ($result as $r){
            $this->assertArrayHasKey('userid',$r, 'Array has key userid');
            $this->assertArrayHasKey('competences',$r, 'Array has key competences');
            $this->assertArrayHasKey('name', $r, 'Array has key name');
            $this->assertArrayHasKey('surname', $r, 'Array has key surname');
            $this->assertArrayHasKey('email',$r, 'Array has key email');
            $this->assertArrayHasKey('phonenumber',$r, 'Array has key phonenumber');
            $this->assertArrayHasKey('birthdate',$r, 'Array has key birthdate');
            $this->assertArrayHasKey('unavailability',$r, 'Array has key unavailability');
            $this->assertArrayHasKey('unavailability',$r, 'Array has key unavailability');
        }}
}


