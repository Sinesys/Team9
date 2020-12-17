<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/materialManagementService.php');

class MaterialManagementServiceTest extends TestCase
{
    private $systemUserModel;

  protected function setUp() : void
    {
        $this->systemUserModel = new MaterialManagementService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');
    }

    public function testInsertMaterialSuccesful(){
    $this->assertNull($this->systemUserModel->insertMaterial('test','test'));
    }
    /**
     * @depends testInsertMaterialSuccesful
     */
    public function testInsertSameMaterial(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->insertMaterial('test','test');}

    public function testInsertMaterialWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->insertMaterial('','test');}

    public function testInsertMaterialWrongNameFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->insertMaterial('test1','');}


    public function testGetMaterialsSuccesful(){
        $result = $this->systemUserModel->getMaterials();
        foreach ($result as $r){
            $this->assertArrayHasKey('materialid',$r, 'Array has key materialid');
            $this->assertArrayHasKey('name',$r, 'Array has key name');
        }}

    public function testGetMaterialSuccesful(){
        $r = $this->systemUserModel->getMaterial('test');
        $this->assertArrayHasKey('materialid',$r, 'Array has key materialid');
        $this->assertArrayHasKey('name',$r, 'Array has key name');}

    public function testGetMaterialWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->getMaterial('wrongID');}

    public function testGetMaterialWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->getMaterial('');}


    public function testUpdateMaterialSuccesful(){
        $this->assertNull($this->systemUserModel->editMaterial('test','update'));}

    public function testUpdateMaterialWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->editMaterial('wrongID','update');}

    public function testUpdateMaterialWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->editMaterial('','test');}

    public function testUpdateMaterialWrongNameFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->editMaterial('test','');}


    public function testDeleteMaterialWrongID(){
        $this->expectException(WrongDataException::class);
        $this->systemUserModel->deleteMaterial('wrongID');}

    public function testDeleteMaterialWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->systemUserModel->deleteMaterial('');}

    public function testDeleteMaterialSuccesful(){
        $this->assertNull($this->systemUserModel->deleteMaterial('test'));}


}
