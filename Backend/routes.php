<?php
require_once('libs/api.php');
require_once('Services/usersManagementService.php');
require_once('Services/loginService.php');
require_once('Services/competencesManagementService.php');
require_once('Services/accessLogService.php');
require_once('Services/activityManagementService.php');
require_once('Services/activityAssignmentService.php');
require_once('Services/mantainerManagementService.php');

$api = new API;


header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-type: application/json');

$api->get('/', function(){
    echo "API MOPP UPKEEP";
});

// ACCESS LOG
$api->get('/accesslog', function(){
    $service = new AccessLogService('localhost', '5432');
    $service->get();
});

// COMPETENCES MANAGEMENT
$api->get('/competences', function(){
    $service = new CompetencesManagementService('localhost', '5432');
    $service->get();
});

// USERS MANAGEMENT
$api->get('/users', function(){
    $service = new UsersManagementService('localhost', '5432');
    $service->get();
});

$api->get('/users/{}', function($id){
    $service = new UsersManagementService('localhost', '5432');
    $service->get($id);
});

$api->post('/users', function(){
    $service = new UsersManagementService('localhost', '5432');
    $service->post(file_get_contents("php://input"));
});

$api->delete('/users/{id}', function($id){
    $service = new UsersManagementService('localhost', '5432');
    $service->delete($id);
});

$api->put('/users/{id}', function($id){
    $service = new UsersManagementService('localhost', '5432');
    $service->put(file_get_contents("php://input"), $id);
});


// MAINTAINER MANAGEMENT
$api->get('/maintainers', function(){
    $service = new MantainerManagementService;
    $service->get();
});

$api->get('/maintainers/{id}', function($id){
    $service = new MantainerManagementService;
    $service->get($id);
});


// LOGIN SERVICE
$api->post('/login', function(){
    $service = new loginService;
    $service->post(file_get_contents("php://input"));
});

$api->post('/logout', function(){
    $service = new logoutService;
    $service->post(file_get_contents("php://input"));
});

// ACTIVITY MANAGEMENT
$api->get('/activities', function(){
    $service = new ActivityManagementService;
    $service->get();
});

$api->get('/activities/{activityid}', function($activityid){
    $service = new ActivityManagementService;
    $service->get($activityid);
});

$api->post('/activities', function(){
    $service = new ActivityManagementService;
    $service->post(file_get_contents("php://input"));
});

$api->put('/activities/{activityid}', function($activityid){
    $service = new ActivityManagementService;
    $service->put(file_get_contents("php://input"), $activityid);
});

$api->delete('/activities/{activityid}', function($activityid){
    $service = new ActivityManagementService;
    $service->delete($activityid);
});

// ACTIVITY ASSIGNEMENT MANAGEMENT
$api->post('/assignactivity/{id}', function($id){
    $service = new ActivityAssignementService;
    $service->post(file_get_contents("php://input"),$id);
});

$api->delete('/assignactivity/{id}', function($id){
    $service = new ActivityAssignementService;
    $service->delete($id);
});

$api->route($_SERVER);
