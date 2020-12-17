<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/activityTypologyManagementService.php');

class ActivityTypologyManagementServiceTest extends TestCase{
    private $systemUserModel;
    protected function setUp() : void{
        $this->systemUserModel = new ActivityTypologyManagementService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');}


    public function testInsertActivityTypologySuccesful(){
        $this->assertNull($this->systemUserModel->insertActivityTypology('test','test'));}
    /**
     * @depends testInsertActivityTypologySuccesful
     */
    public function testInsertSameActivityTypology(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->insertActivityTypology('test','test');}

    public function testInsertActivityTypologyWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->insertActivityTypology('','test');}

    public function testGetActivityTypologiesSuccesful(){
        $result = $this->systemUserModel->getActivityTypologies();
        foreach ($result as $r){
            $this->assertArrayHasKey('typologyid',$r, 'Array has key typologyid');
            $this->assertArrayHasKey('description',$r, 'Array has key description');}}

    public function testGetActivityTypologySuccesful(){
        $r = $this->systemUserModel->getActivityTypology('test');
        $this->assertArrayHasKey('typologyid',$r, 'Array has key typologyid');
        $this->assertArrayHasKey('description',$r, 'Array has key description');}

    public function testGetActivityTypologyWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->getActivityTypology('wrongID');}

    public function testGetActivityTypologyWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->getActivityTypology('');}

    public function testUpdateActivityTypologySuccesful(){
        $this->assertNull($this->systemUserModel->editActivityTypology('test','update'));}
    public function testUpdateActivityTypologyWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->editActivityTypology('wrongID','update');}
    public function testUpdateActivityTypologyWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->editActivityTypology('','test');}

    public function testDeleteActivityTypologyWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->deleteActivityTypology('wrongID');}
    public function testDeleteActivityTypologyWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->deleteActivityTypology('');}
    public function testDeleteActivityTypologySuccesful(){
        $this->assertNull($this->systemUserModel->deleteActivityTypology('test'));}


}
