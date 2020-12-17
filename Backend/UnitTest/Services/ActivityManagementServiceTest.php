<?php
use PHPUnit\Framework\TestCase;
require_once(__DIR__.'/../../Services/activityManagementService.php');

class ActivityManagementServiceTest extends TestCase
{
    private $activityManagementService;

    protected function setUp() : void
    {
        $this->activityManagementService = new ActivityManagementService('api-team9.ddns.net', '5432', 'SEProjectUnitTest', 'se_user', 'team9user');
    }

    public function testAddActivitySuccessful()
    {
        $id = 'UnitTestID';
        $activityinfo = [
            'activityid' => $id,
            'description' => 'Description',
            'scheduledweek' => 1,
            "estimatedtime" => 45,
            "site" => "site1",
            "typology" => "typology1",
            "procedure" => "procedure1",
            "interruptible" => true,
            "materials"=> ["material1", "material2"]
        ];
        $result = $this->activityManagementService->addActivity($activityinfo);
        $this->assertNull($result);
        return $id;
    }

    /**
     * @depends testAddActivitySuccessful
     */
    public function testAddSameActivity($id)
    {
        $this->expectException(WrongDataException::class);
        $activityinfo = [
            'activityid' => $id,
            'description' => 'Description',
            'scheduledweek' => 1,
            "estimatedtime" => 45,
            "site" => "site1",
            "typology" => "typology1",
            "procedure" => "procedure1",
            "interruptible" => true,
            "materials"=> ["material1", "material2"]
        ];
        $result = $this->activityManagementService->addActivity($activityinfo);
    }

    /**
     * @dataProvider wrongDataFormatProvider
     */
    public function testAddActivityWrongDataFormat($activityid, $description, $scheduledweek)
    {
        $this->expectException(WrongDataFormatException::class);
        $activityinfo = [
            'activityid' => $activityid,
            'description' => $description,
            'scheduledweek' => $scheduledweek,
            "estimatedtime" => 45,
            "site" => "site1",
            "typology" => "typology1",
            "procedure" => "procedure1",
            "interruptible" => true,
            "materials"=> ["material1", "material2"]
        ];
        $this->activityManagementService->addActivity($activityinfo);
    }

    public function testAddActivityWrongData()
    {
        $this->expectException(WrongDataException::class);
        $activityinfo = [
            'activityid' => 'ActivityTest2',
            'description' => 'description',
            'scheduledweek' => 1,
            "estimatedtime" => 45,
            "site" => "site1",
            "typology" => "typology1",
            "procedure" => "procedure1",
            "interruptible" => true,
            "materials"=> ["wrongmaterial"]
        ];
        $this->activityManagementService->addActivity($activityinfo);
    }

    /**
     * @depends testAddActivitySuccessful
     */
    public function testEditActivitySuccefull($activityid)
    {
        $activityinfo = [
            'description' => 'EDIT',
            'scheduledweek' => 2,
            "estimatedtime" => 60,
            "site" => "site1",
            "typology" => "typology1",
            "procedure" => "procedure1",
            "interruptible" => false,
            "materials"=> ["material2"]
        ];
        $result = $this->activityManagementService->editActivity($activityid, $activityinfo);
        $this->assertNull($result);
    }

    /**
     * @dataProvider wrongDataFormatProvider
     */
    public function testEditActivityWrongDataFormat($activityid, $description, $scheduledweek)
    {
        $activityinfo = [
            'description' => $description,
            'scheduledweek' => $scheduledweek,
            "estimatedtime" => 45,
            "site" => "site1",
            "typology" => "typology1",
            "procedure" => "procedure1",
            "interruptible" => true,
            "materials"=> ["material1", "material2"]
        ];
        $this->expectException(WrongDataFormatException::class);
        $this->activityManagementService->editActivity($activityid, $activityinfo);
    }

    public function testEditActivityWrongData()
    {
        $this->expectException(WrongDataException::class);
        $activityinfo = [
            'description' => 'DescriptionEdit',
            'scheduledweek' => 2,
            "estimatedtime" => 45,
            "site" => "site1",
            "typology" => "typology1",
            "procedure" => "procedure1",
            "interruptible" => true,
            "materials"=> ["material1", "material2"]
        ];
        $result = $this->activityManagementService->editActivity('notexists', $activityinfo);
    }

    /**
     * @depends testAddActivitySuccessful
     */
    public function testGetActivitiesInfoSuccessful(){
        $result = $this->activityManagementService->getActivitiesInfo();
        $elem = $result[0];
        $this->assertArrayHasKey('activityid', $elem);
        $this->assertArrayHasKey('description', $elem);
        $this->assertArrayHasKey('scheduledweek', $elem);
        $this->assertArrayHasKey('estimatedtime', $elem);
        $this->assertArrayHasKey('site', $elem);
        $this->assertArrayHasKey('typology', $elem);
        $this->assertArrayHasKey('procedure', $elem);
        $this->assertArrayHasKey('interruptible', $elem);
        $this->assertArrayHasKey('assignedto', $elem);
        $this->assertArrayHasKey('smpfile', $elem);
    }

    /**
     * @depends testAddActivitySuccessful
     */
    public function testGetActivityInfoSuccessful($activityid)
    {
        $elem = $this->activityManagementService->getActivityInfo($activityid);
        $this->assertArrayHasKey('activityid', $elem);
        $this->assertArrayHasKey('description', $elem);
        $this->assertArrayHasKey('scheduledweek', $elem);
        $this->assertArrayHasKey('estimatedtime', $elem);
        $this->assertArrayHasKey('site', $elem);
        $this->assertArrayHasKey('typology', $elem);
        $this->assertArrayHasKey('procedure', $elem);
        $this->assertArrayHasKey('interruptible', $elem);
        $this->assertArrayHasKey('assignedto', $elem);
        $this->assertArrayHasKey('smpfile', $elem);
    }

    public function testGetActivityInfoWrongData()
    {
        $this->expectException(WrongDataException::class);
        $this->activityManagementService->getActivityInfo('notexists');
    }

    public function testGetActivityInfoWrongDataFormat()
    {
        $this->expectException(WrongDataFormatException::class);
        $this->activityManagementService->getActivityInfo('wrong format');
    }

    /**
     * @depends testAddActivitySuccessful
     */
    public function testAddAssignmentActivitySuccessful($id){
        $info = [
            'userid' => 'MANT_TEST',
            'day' => '2025-01-01',
            'starttime' => '12:00',
            'endtime' => '15:00'
        ];
        $result = $this->activityManagementService->addAssignmentActivity($id, $info);
        $this->assertNull($result);
        return 'MANT_TEST';
    }

    /**
     * @depends testAddAssignmentActivitySuccessful
     */
    public function testAddSameAssignmentActivity($id){
        $this->expectException(WrongDataException::class);
        $info = [
            'userid' => 'MANT_TEST',
            'day' => '2025-01-01',
            'starttime' => '12:00',
            'endtime' => '15:00'
        ];
        $this->activityManagementService->addAssignmentActivity($id, $info);
    }

    /**
     * @depends testAddActivitySuccessful
     * @depends testAddAssignmentActivitySuccessful
     */
    public function testDeleteAssignmentActivitySuccessful($id){
        $result = $this->activityManagementService->deleteAssignmentActivity($id);
        $this->assertNull($result);
    }

    public function testDeleteAssignmentActivityWrongDataFormat(){
        $this->expectException(WrongDataFormatException::class);
        $this->activityManagementService->deleteAssignmentActivity('wrong format');
    }

    public function testDeleteAssignmentActivityWrongData(){
        $this->expectException(WrongDataException::class);
        $this->activityManagementService->deleteAssignmentActivity('notexists');
    }

    /**
     * @depends testAddActivitySuccessful
     */
    public function testGetActivitiesInfoVerboseSuccessful(){
        $result = $this->activityManagementService->getActivitiesInfoVerbose();
        $elem = $result[0];
        $this->assertArrayHasKey('activityid', $elem);
        $this->assertArrayHasKey('description', $elem);
        $this->assertArrayHasKey('scheduledweek', $elem);
        $this->assertArrayHasKey('estimatedtime', $elem);
        $this->assertArrayHasKey('site', $elem);
        $this->assertIsArray($elem['site']);
        $this->assertArrayHasKey('typology', $elem);
        $this->assertIsArray($elem['typology']);
        $this->assertArrayHasKey('procedure', $elem);
        $this->assertIsArray($elem['procedure']);
        $this->assertArrayHasKey('interruptible', $elem);
        $this->assertArrayHasKey('assignedto', $elem);
        $this->assertArrayHasKey('smpfile', $elem);
    }

    /**
     * @depends testAddActivitySuccessful
     */
    public function testGetActivityInfoVerboseSuccessful($id){
        $elem = $this->activityManagementService->getActivityInfoVerbose($id);
        $this->assertArrayHasKey('activityid', $elem);
        $this->assertArrayHasKey('description', $elem);
        $this->assertArrayHasKey('scheduledweek', $elem);
        $this->assertArrayHasKey('estimatedtime', $elem);
        $this->assertArrayHasKey('site', $elem);
        $this->assertIsArray($elem['site']);
        $this->assertArrayHasKey('typology', $elem);
        $this->assertIsArray($elem['typology']);
        $this->assertArrayHasKey('procedure', $elem);
        $this->assertIsArray($elem['procedure']);
        $this->assertArrayHasKey('interruptible', $elem);
        $this->assertArrayHasKey('assignedto', $elem);
        $this->assertArrayHasKey('smpfile', $elem);
    }

    public function testGetActivityInfoVerboseWrongData(){
        $this->expectException(WrongDataException::class);
        $elem = $this->activityManagementService->getActivityInfoVerbose('notexists');
    }

    public function testGetActivityInfoVerboseWrongDataForma(){
        $this->expectException(WrongDataFormatException::class);
        $elem = $this->activityManagementService->getActivityInfoVerbose('wrong format');
    }

    /**
     * @depends testAddActivitySuccessful
     */
    public function testDeleteActivitySuccessful($id)
    {
        $result = $this->activityManagementService->deleteActivity($id);
        $this->assertNull($result);
    }

    public function testDeleteActivityWrongDataFormat()
    {
        $this->expectException(WrongDataFormatException::class);
        $this->activityManagementService->deleteActivity('WRONG FORMAT ID');
    }

    public function testDeleteActivityWrongData()
    {
        $this->expectException(WrongDataException::class);
        $this->activityManagementService->deleteActivity('notexists');
    }

    public function wrongDataFormatProvider()
    {
        yield 'Bad ID' =>['Bad ID', 'Description', 1];
        yield 'Bad Week' =>['UnitTestID2', 'Description', 100];
    }

}
