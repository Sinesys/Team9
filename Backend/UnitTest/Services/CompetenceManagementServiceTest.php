<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/competencesManagementService.php');

class CompetenceManagementServiceTest extends TestCase{
    private $systemUserModel;

    protected function setUp() : void{
        $this->systemUserModel = new CompetencesManagementService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');}


    public function testInsertCompetenceSuccesful(){
        $this->assertNull($this->systemUserModel->insertCompetence('test','test','test'));}

    /**
     * @depends testInsertCompetenceSuccesful
     */
    public function testInsertSameCompetence(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->insertCompetence('test','test','test');}

    public function testInsertCompetenceWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->insertCompetence('','test','test');}

    public function testInsertCompetenceWrongNameFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->insertCompetence('test1','','test');}



    public function testGetCompetencesSuccesful(){
        $result = $this->systemUserModel->getCompetences();
        foreach ($result as $r){
            $this->assertArrayHasKey('competenceid',$r, 'Array has key competenceid');
            $this->assertArrayHasKey('name',$r, 'Array has key name');
        }}

    public function testGetCompetenceSuccesful(){
        $r = $this->systemUserModel->getCompetence('test');
        $this->assertArrayHasKey('competenceid',$r, 'Array has key competenceid');
        $this->assertArrayHasKey('name',$r, 'Array has key name');}

    public function testGetCompetenceWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->getCompetence('wrongID');}

    public function testGetCompetenceWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->getCompetence('');}


    public function testUpdateCompetenceSuccesful(){
        $this->assertNull($this->systemUserModel->editCompetence('test','update','update'));}

    public function testUpdateCompetenceWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->editCompetence('wrongID','update','update');}

    public function testUpdateCompetenceWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->editCompetence('','test','test');}

    public function testUpdateCompetenceWrongNameFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->editCompetence('test','','test');}



    public function testDeleteCompetenceWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->deleteCompetence('wrongID');}

    public function testDeleteCompetenceWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->deleteCompetence('');}

    public function testDeleteCompetenceSuccesful(){
        $this->assertNull($this->systemUserModel->deleteCompetence('test'));}


}