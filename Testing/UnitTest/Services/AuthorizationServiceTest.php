<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/accessService.php');
require_once(__DIR__.'/../../Services/authorizationService.php');

class AuthorizationServiceTest extends TestCase
{
    private $authorizationService;

    protected function setUp() : void
    {
        $this->authorizationService = new AuthorizationService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');
    }

    public function testIsAuthorizatedSuccessful(){
        $accessService = new accessService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');
        $result = $accessService->login('ADM_TEST', 'password');
        $check = $this->authorizationService->isAuthorizated($result['auth_token'], ['ADM']);
        $this->assertTrue($check);
        return $result['auth_token'];
    }

    /**
     * @depends testIsAuthorizatedSuccessful
     */
    public function testIsAuthorizatedFailed($token){
        $check = $this->authorizationService->isAuthorizated($token, ['MNT']);
        $this->assertFalse($check);
    }

    public function testIsAuthorizatedWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $check = $this->authorizationService->isAuthorizated('', ['ADM']);
    }

    /**
     * @depends testIsAuthorizatedSuccessful
     */
    public function testMatchTokenWithIDSuccessful($token){
        $check = $this->authorizationService->matchTokenWithID($token, 'ADM_TEST');
        $this->assertTrue($check);
    }

    /**
     * @depends testIsAuthorizatedSuccessful
     */
    public function testMatchTokenWithIDFailed($token){
        $check = $this->authorizationService->matchTokenWithID($token, 'WRONG_ID');
        $this->assertFalse($check);
    }

    public function testMatchTokenWithIDWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $check = $this->authorizationService->matchTokenWithID('', 'ADM');
    }

}
