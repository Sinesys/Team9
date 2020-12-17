<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/usersManagementService.php');

class UsersManagementServiceTest extends TestCase
{
    private $usersManagementService;

    public function setUp(): void
    {
        $this->usersManagementService = new UsersManagementService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');
    }

    public function testAddUserSuccessful()
    {
        $userinfo = [
            'userid' => 'UnitTestUser',
            'password' => 'UnitTestPassword',
            'role' => 'ADM',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
        ];
        $this->assertNull($this->usersManagementService->addUser($userinfo));
        return 'UnitTestUser';
    }

    /**
     * @depends testAddUserSuccessful
     */
    public function testAddSameUser($id)
    {
        $this->expectException(WrongDataException::class);
        $userinfo = [
            'userid' => $id,
            'password' => 'UnitTestPassword',
            'role' => 'ADM',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
        ];
        $this->usersManagementService->addUser($userinfo);
    }

    public function testAddUserWrongDataFormat()
    {
        $this->expectException(WrongDataFormatException::class);
        $userinfo = [
            'userid' => 'WRONG ID',
            'password' => 'UnitTestPassword',
            'role' => 'ADM',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
        ];
        $this->usersManagementService->addUser($userinfo);
    }

    public function testAddUserWrongDataFormat2()
    {
        $this->expectException(WrongDataFormatException::class);
        $userinfo = [
            'userid' => 'rightid',
            'password' => 'UnitTestPassword',
            'role' => 'WRONG',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
        ];
        $this->usersManagementService->addUser($userinfo);
    }

    public function testAddUserMaintainerSuccessful()
    {
        $userinfo = [
            'userid' => 'UTUMaintainer',
            'password' => 'UnitTestPassword',
            'role' => 'MNT',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
            'competences' => []
        ];
        $this->assertNull($this->usersManagementService->addUser($userinfo));
        return 'UTUMaintainer';
    }

    public function testAddUserMaintainerWrongCompetences()
    {
        $userinfo = [
            'userid' => 'UTUMaintainer2',
            'password' => 'UnitTestPassword',
            'role' => 'MNT',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
            'competences' => ["compidwrong0"]
        ];
        $exception = false;
        try {
            $this->usersManagementService->addUser($userinfo);
        } catch (WrongDataException $e) {
            $exception = true;
        }
        $this->assertTrue($exception, 'Add user with maintainer role, with wrong competences');
    }

    public function testAddUserMaintainerWrongDataFormat()
    {
        $userinfo = [
            'userid' => 'UTUMaintainer2',
            'password' => 'UnitTestPassword',
            'role' => 'MNT',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
            'competences' => ["Wrong Competence!"]
        ];
        $exception = false;
        try {
            $this->usersManagementService->addUser($userinfo);
        } catch (WrongDataFormatException $e) {
            $exception = true;
        }
        $this->assertTrue($exception, 'Add user with maintainer role, with wrong competences');
    }

    public function testGetUsersInfoSuccefull()
    {
        $result = $this->usersManagementService->getUsersInfo();
        $this->assertIsArray($result, 'Assert is array');
        $this->assertArrayHasKey('userid', $result[0]);
        $this->assertArrayHasKey('name', $result[0]);
        $this->assertArrayHasKey('surname', $result[0]);
        $this->assertArrayHasKey('email',$result[0]);
        $this->assertArrayHasKey('phonenumber', $result[0]);
        $this->assertArrayHasKey('birthdate',$result[0]);
        $this->assertArrayHasKey('role',$result[0]);
        if($result[0]['role']==='MNT')
            $this->assertArrayHasKey('competences',$result[0]);
    }

    /**
     * @depends testAddUserSuccessful
     */
    public function testGetUserInfo()
    {
        $result = $this->usersManagementService->getUserInfo('UnitTestUser');
        $this->assertArrayHasKey('userid', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('surname', $result);
        $this->assertArrayHasKey('email',$result);
        $this->assertArrayHasKey('phonenumber', $result);
        $this->assertArrayHasKey('birthdate',$result);
        $this->assertArrayHasKey('role',$result);
    }

    /**
     * @depends testAddUserMaintainerSuccessful
     */
    public function testGetUserInfoMaintainerSuccefull($id)
    {
        $result = $this->usersManagementService->getUserInfo($id);
        $this->assertIsArray($result, 'Assert is array');
        $this->assertArrayHasKey('userid', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('surname', $result);
        $this->assertArrayHasKey('email',$result);
        $this->assertArrayHasKey('phonenumber', $result);
        $this->assertArrayHasKey('birthdate',$result);
        $this->assertArrayHasKey('role',$result);
        $this->assertArrayHasKey('competences',$result);
    }

    /**
     * @depends testAddUserSuccessful
     */
    public function testGetUserInfoWrongID()
    {
        $this->expectException(WrongDataException::class);
        $this->usersManagementService->getUserInfo('WrongID');
    }

    /**
     * @depends testAddUserSuccessful
     */
    public function testEditUserSuccessful($id)
    {
        $userinfo = [
            'password' => 'UnitTestPassword',
            'role' => 'ADM',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
        ];
        $this->assertNull($this->usersManagementService->editUser($id, $userinfo));
    }
    public function testEditUserWrongID()
    {
        $this->expectException(WrongDataException::class);
        $userinfo = [
            'password' => 'UnitTestPassword',
            'role' => 'ADM',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
        ];
        $this->usersManagementService->editUser('notexists',$userinfo);
    }
    public function testEditUserWrongDataFormat()
    {
        $this->expectException(WrongDataFormatException::class);
        $userinfo = [
            'password' => 'UnitTestPassword',
            'role' => 'ADM',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
        ];
        $this->usersManagementService->editUser('WRONG ID',$userinfo);
    }

    /**
     * @depends testAddUserMaintainerSuccessful
     */
    public function testEditUserMaintainerSuccessful($id)
    {
        $userinfo = [
            'password' => 'UnitTestPassword',
            'role' => 'MNT',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
            'competences' => ['PS']
        ];
        $this->assertNull($this->usersManagementService->editUser($id, $userinfo));
    }

    /**
     * @depends testAddUserMaintainerSuccessful
     */
    public function testEditUserMaintainerWrongCompetences($id)
    {
        $this->expectException(WrongDataException::class);
        $userinfo = [
            'password' => 'UnitTestPassword',
            'role' => 'MNT',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
            'competences' => ["compidwrong0"]
        ];
        $this->assertNull($this->usersManagementService->editUser($id, $userinfo));
    }

    /**
     * @depends testAddUserMaintainerSuccessful
     */
    public function testEditUserMaintainerWrongDataCompetences($id)
    {
        $this->expectException(WrongDataException::class);
        $userinfo = [
            'password' => 'UnitTestPassword',
            'role' => 'MNT',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01'
        ];
        $this->assertNull($this->usersManagementService->editUser($id, $userinfo));
    }

    /**
     * @depends testAddUserMaintainerSuccessful
     */
    public function testEditUserMaintainerWrongDataFormat($id)
    {
        $this->expectException(WrongDataFormatException::class);
        $userinfo = [
            'password' => 'UnitTestPassword',
            'role' => 'MNT',
            'name' => 'Unit',
            'surname' => 'Test',
            'email' => 'unit@test.com',
            'phonenumber' => '0000000000',
            'birthdate' => '1990-01-01',
            'competences' => ["Wrong Competence!"]
        ];
        $this->assertNull($this->usersManagementService->editUser($id, $userinfo));
    }

    /**
     * @depends testAddUserSuccessful
     */
    public function testDeleteUserSuccessful($id)
    {
        $result = $this->usersManagementService->deleteUser($id);
        $this->assertNull($result);
    }

    public function testDeleteUserWrongData()
    {
        $this->expectException(WrongDataException::class);
        $this->usersManagementService->deleteUser('UserNotExists');
    }

    public function testDeleteUserWrongDataFormat()
    {
        $this->expectException(WrongDataFormatException::class);
        $this->usersManagementService->deleteUser('Wrong format');
    }

    /**
     * @depends testAddUserMaintainerSuccessful
     */
    public function testDeleteUserMaintainerSuccessful()
    {
        $result = $this->usersManagementService->deleteUser('UTUMaintainer');
        $this->assertNull($result);
    }

}