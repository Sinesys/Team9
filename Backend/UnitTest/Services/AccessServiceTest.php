<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/accessService.php');

class AccessServiceTest extends TestCase
{
    private $accessService;

    protected function setUp(): void
    {
        $this->accessService = new AccessService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');
    }

    public function testLoginSuccessful()
    {
        $result = $this->accessService->login('ADM_TEST', 'password');
        $this->assertArrayHasKey('role', $result, 'Array has key role');
        $this->assertArrayHasKey('auth_token', $result, 'Array has key auth_toke');
        return $result['auth_token'];
    }

    public function testLoginWrongUsername()
    {
        $this->expectException(WrongCredentialsException::class);
        $this->accessService->login('WRONGUSERNAME', 'admin');
    }

    public function testLoginWrongPassword()
    {
        $this->expectException(WrongCredentialsException::class);
        $this->accessService->login('ADM_TEST', 'WRONGPASSWORD');
    }

    public function testLoginWrongDataFormat()
    {
        $this->expectException(WrongDataFormatException::class);
        $this->accessService->login('not acceptable', 'password');
    }

    /**
     * @depends testLoginSuccessful
     */
    public function testLogoutSuccessful($token)
    {
        $result = $this->accessService->logout($token);;
        $this->assertNull($result);
    }

    public function testLogoutWrongToken()
    {
        $this->expectException(WrongDataException::class);
        $this->accessService->logout('This Token Is Wrong');
    }

    public function testLogoutWrongDataFormat()
    {
        $this->expectException(WrongDataFormatException::class);
        $this->accessService->logout('');
    }

    public function testGetAccessLogSuccessful()
    {
        $accessService = new accessService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');
        $result = $accessService->login('ADM_TEST', 'password');
        $result = $this->accessService->getAccessLog();
        $this->assertArrayHasKey('userid', $result[0]);
        $this->assertArrayHasKey('accesstime', $result[0]);
    }
}
