<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/siteManagementService.php');

class SiteManagementServiceTest extends TestCase{
    private $systemUserModel;

    protected function setUp() : void{
        $this->systemUserModel = new SiteManagementService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');}


    public function testInsertSiteSuccesful(){
        $this->assertNull($this->systemUserModel->insertSite('test','test','test'));}

    /**
     * @depends testInsertSiteSuccesful
     */
    public function testInsertSameSite(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->insertSite('test','test','test');}

    public function testInsertSiteWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->insertSite('','test','test');}

    public function testInsertSiteWrongAreaFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->insertSite('test1','','test');}

    public function testInsertSiteWrongDepartmentFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->insertSite('test1','test','');}


    public function testGetSitesSuccesful(){
        $result = $this->systemUserModel->getSites();
        foreach ($result as $r){
            $this->assertArrayHasKey('siteid',$r, 'Array has key siteid');
            $this->assertArrayHasKey('area',$r, 'Array has key area');
            $this->assertArrayHasKey('department',$r, 'Array has key department');
        }}

    public function testGetSiteSuccesful(){
        $r = $this->systemUserModel->getSite('test');
        $this->assertArrayHasKey('siteid',$r, 'Array has key siteid');
        $this->assertArrayHasKey('area',$r, 'Array has key area');
        $this->assertArrayHasKey('department',$r, 'Array has key department');}

    public function testGetSiteWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->getSite('wrongID');}

    public function testGetSiteWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->getSite('');}


    public function testUpdateSiteSuccesful(){
        $this->assertNull($this->systemUserModel->editSite('test','update','update'));}

    public function testUpdateSiteWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->editSite('wrongID','update','update');}

    public function testUpdateSiteWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->editSite('','test','test');}

    public function testUpdateSiteWrongAreaFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->editSite('test','','test');}

    public function testUpdateSiteWrongDepartmentFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->editSite('test','test','');}


    public function testDeleteSiteWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->deleteSite('wrongID');}

    public function testDeleteSiteWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->deleteSite('');}

    public function testDeleteSiteSuccesful(){
        $this->assertNull($this->systemUserModel->deleteSite('test'));}


}