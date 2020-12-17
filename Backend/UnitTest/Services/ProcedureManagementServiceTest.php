<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/procedureManagementService.php');

class ProcedureManagementServiceTest extends TestCase{
    private $service;

    protected function setUp() : void{
        $this->service = new ProcedureManagementService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');}

    public function testMakeDir(){
        new ProcedureManagementService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user', 'TestDir');
        $this->assertTrue(is_dir('TestDir'));
        rmdir('TestDir');
        $this->assertFalse(is_dir('TestDir'));
    }

    public function testInsertProcedureSuccesful(){
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>['competence1']
        ];
        $this->assertNull($this->service->insertProcedure('test',$procedureinfo));
        return 'test';}

    /**
     * @depends testInsertProcedureSuccesful
     */
    public function testInsertSameProcedure(){
        $this->expectException(WrongDataException::class);
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>[]
        ];
        $this->service->insertProcedure('test',$procedureinfo);}

    public function testInsertProcedureWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>[]
        ];
        $this->service->insertProcedure('',$procedureinfo);}

    public function testInsertProcedureWrongCompetenceFormat(){
        $this->expectException(WrongDataFormatException::class);
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>['not available']
        ];
        $this->service->insertProcedure('test00',$procedureinfo);}

    public function testInsertProcedureWrongCompetence(){
        $this->expectException(WrongDataException::class);
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>['notavailable']
        ];
        $this->service->insertProcedure('test00',$procedureinfo);}


    public function testGetProceduresSuccesful(){
        $result = $this->service->getProcedures();
        foreach ($result as $r){
            $this->assertArrayHasKey('procedureid',$r, 'Array has key procedureid');
            $this->assertArrayHasKey('description',$r, 'Array has key description');
            $this->assertArrayHasKey('competencesrequired',$r, 'Array has key competencesrequired');
            $this->assertArrayHasKey('smpfile', $r);
        }}

    public function testGetProceduresVerboseSuccesful(){
        $result = $this->service->getProceduresVerbose();
        foreach ($result as $r){
            $this->assertArrayHasKey('procedureid',$r, 'Array has key procedureid');
            $this->assertArrayHasKey('description',$r, 'Array has key description');
            $this->assertArrayHasKey('competencesrequired',$r, 'Array has key competencesrequired');
            $this->assertArrayHasKey('smpfile', $r);
        }}

    public function testGetProcedureSuccesful(){
        $r = $this->service->getProcedure('test');
        $this->assertArrayHasKey('procedureid',$r, 'Array has key procedureid');
        $this->assertArrayHasKey('description',$r, 'Array has key description');
        $this->assertArrayHasKey('competencesrequired',$r, 'Array has key competencesrequired');
        $this->assertArrayHasKey('smpfile', $r);}

    public function testGetProcedureWrongID(){
        $this->expectException(WrongDataException::class);
        $this->service->getProcedure('wrongID');}

    public function testGetProcedureWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->service->getProcedure('');}


    public function testUpdateProcedureSuccesful(){
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>[]
        ];
        $this->assertNull($this->service->editProcedure('test',$procedureinfo));}

    public function testUpdateProcedureWrongID(){
        $this->expectException(WrongDataException::class);
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>[]
        ];
        $this->service->editProcedure('wrongID',$procedureinfo);}

    public function testUpdateProcedureWrongIDFormat(){
        $this->expectException(WrongDataFormatException::class);
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>[]
        ];
        $this->service->editProcedure('',$procedureinfo);}

    public function testUpdateProcedureWrongCompetence(){
        $this->expectException(WrongDataException::class);
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>['notavailable']
        ];
        $this->service->editProcedure('test',$procedureinfo);}

    public function testUpdateProcedureWrongCompetenceFormat(){
        $this->expectException(WrongDataFormatException::class);
        $procedureinfo=[
            'description'=>'test',
            'competencesrequired'=>['not available']
        ];
        $this->service->editProcedure('test',$procedureinfo);}

    /**
     * @depends testInsertProcedureSuccesful
     */
    public function testInsertSMPFileServerError($id){
        $this->expectException(ServerErrorException::class);
        $result = $this->service->insertSMPFile($id,null,'test test test');
    }

    /**
     * @depends testInsertProcedureSuccesful
     */
    public function testInsertSMPFile($id){
        $result = $this->service->insertSMPFile($id,'test.txt','test test test');
        $this->assertNull($result);
        return $id;
    }

    /**
     * @depends testInsertSMPFile
     */
    public function testInsertSameSMPFile($id){
        $this->expectException(WrongDataException::class);
        $result = $this->service->insertSMPFile($id,'test.txt','test test test');
    }

    public function testInsertSMPFileWrongData(){
        $this->expectException(WrongDataException::class);
        $result = $this->service->insertSMPFile('notexists','test.txt','test test test');
    }

    public function testInsertSMPFileWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $result = $this->service->insertSMPFile('wrong format','test.txt','test test test');
    }

    /**
     * @depends testInsertProcedureSuccesful
     */
    public function testGetSMPFile($id){
        $result = $this->service->getSMPFile($id);
        $this->assertSame(file_get_contents($result), 'test test test');
    }

    public function testGetSMPFileWrongProcedureID(){
        $this->expectException(WrongDataException::class);
        $result = $this->service->getSMPFile('notexists');
    }

    public function testGetSMPFileWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $result = $this->service->getSMPFile('wrong format');
    }

    /**
     * @depends testInsertProcedureSuccesful
     */
    public function testDeleteSMPFile($id){
        $result = $this->service->deleteSMPFile($id);
        $this->assertNull($result);
    }

    public function testDeleteSMPFileWrongData(){
        $this->expectException(WrongDataException::class);
        $result = $this->service->getSMPFile('notexists');
    }

    public function testDeleteSMPFileWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $result = $this->service->getSMPFile('wrong format');
    }

    public function testDeleteProcedureWrongID(){
        $this->expectException(WrongDataException::class);
        $this->service->deleteProcedure('wrongID');}

    public function testDeleteProcedureWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->service->deleteProcedure('');}

    public function testDeleteProcedureSuccesful(){
        $this->assertNull($this->service->deleteProcedure('test'));}


}