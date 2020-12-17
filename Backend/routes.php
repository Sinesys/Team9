<?php

/* Including all the depedencies */
require_once('libs/rest.php');
require_once('libs/utils.php');
require_once('libs/response.php');
require_once('Services/AccessService.php');
require_once('Services/activityManagementService.php');
require_once('Services/activityTypologyManagementService.php');
require_once('Services/authorizationService.php');
require_once('Services/competencesManagementService.php');
require_once('Services/authorizationService.php');
require_once('Services/maintainerManagementService.php');
require_once('Services/materialManagementService.php');
require_once('Services/procedureManagementService.php');
require_once('Services/siteManagementService.php');
require_once('Services/usersManagementService.php');
require_once('Services/servicesExceptions.php');
require_once('DBModels/dbExceptions.php');


/* Setting the response header */
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Authorization");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-type: application/json');


/* JSON with DB's infos */
$dbinfo = [
    'host' => "api-team9.ddns.net",
    'port' => "5432",
    'dbname' => "SEProject",
    'username' => "se_user",
    'password' => "team9user"
];

// REST class is a Singleton. Getting che instance.
$api = REST::getInstance();

/* Creating the middlewares for the authentication and authorization process */

//Authorization ADMIN
$auth_ADM = $api->middleware('auth_ADM', function () use ($dbinfo) {
    $headers = getallheaders();
    if (!array_key_exists("Authorization", $headers))
        return Response::makeJSONResponse(401, "Unauthorized");

    $auth = new AuthorizationService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        if (!$auth->isAuthorizated($headers["Authorization"], ['ADM']))
            return Response::makeJSONResponse(403, "Unauthorized");
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500);
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'The token is not valid ');
    }
    return true;
});

//authorization PLANNER
$auth_PLN = $api->middleware('auth_PLN', function () use ($dbinfo) {
    $headers = getallheaders();
    if (!array_key_exists("Authorization", $headers))
        return Response::makeJSONResponse(401, "Unauthorized");

    $auth = new AuthorizationService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        if (!$auth->isAuthorizated($headers["Authorization"], ['PLN']))
            return Response::makeJSONResponse(403, "Unauthorized");
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500);
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'The token is not valid ');
    }
    return true;
});

//authorization DB LOADER
$auth_DBL = $api->middleware('auth_DBL', function () use ($dbinfo) {
    $headers = getallheaders();
    if (!array_key_exists("Authorization", $headers))
        return Response::makeJSONResponse(401, "Unauthorized");

    $auth = new AuthorizationService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        if (!$auth->isAuthorizated($headers["Authorization"], ['DBL']))
            return Response::makeJSONResponse(403, "Unauthorized");
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500);
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'The token is not valid ');
    }
    return true;
});

//authorization ADMIN & DB LOADER
$auth_ADM_DBL = $api->middleware('auth_ADM_DBL', function () use ($dbinfo) {
    $headers = getallheaders();
    if (!array_key_exists("Authorization", $headers))
        return Response::makeJSONResponse(401, "Unauthorized");

    $auth = new AuthorizationService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        if (!$auth->isAuthorizated($headers["Authorization"], ['ADM', 'DBL']))
            return Response::makeJSONResponse(403, "Unauthorized");
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500);
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'The token is not valid ');
    }
    return true;
});

//authorization PLANNER & DB LOADER
$auth_PLN_DBL = $api->middleware('auth_PLN_DBL', function () use ($dbinfo) {
    $headers = getallheaders();
    if (!array_key_exists("Authorization", $headers))
        return Response::makeJSONResponse(401, "Unauthorized");

    $auth = new AuthorizationService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        if (!$auth->isAuthorizated($headers["Authorization"], ['PLN', 'DBL']))
            return Response::makeJSONResponse(403, "Unauthorized");
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500);
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'The token is not valid ');
    }
    return true;
});

//authorization ADMIN & PLANNER & DB LOADER
$auth_ADM_PLN_DBL = $api->middleware('auth_ADM_PLN_DBL', function () use ($dbinfo) {
    $headers = getallheaders();
    if (!array_key_exists("Authorization", $headers))
        return Response::makeJSONResponse(401, "Unauthorized");

    $auth = new AuthorizationService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        if (!$auth->isAuthorizated($headers["Authorization"], ['PLN', 'DBL', 'ADM']))
            return Response::makeJSONResponse(403, "Unauthorized");
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500);
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'The token is not valid ');
    }
    return true;
});


/* Registring the routes */

$api->get('/', function () {
    return Response::makeJSONResponse(200, 'API MOPP Upkeep');
});


$api->post('/login', function () use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"), 'id', 'password');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new AccessService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->login($post['id'], $post['password']);
        return Response::makeJSONResponse(200, $data);
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'Json is not valid.');
    } catch (WrongCredentialsException $e) {
        return Response::makeJSONResponse(403, 'The credentials are not correct.');
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'Server problem (auth). Retry later.');
    }
});


$api->post('/logout', function () use ($dbinfo) {
    $headers = getallheaders();
    if (!array_key_exists("Authorization", $headers))
        return Response::makeJSONResponse(401, "Unauthorized");

    $service = new AccessService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->logout($headers["Authorization"]);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'Server Error');
    } catch (WrongDataException | WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'The token is not valid ');
    }
});


$api->get('/accesslog', function () use ($dbinfo) {
    $service = new AccessService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getAccessLog();
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError ACCESS LOG');
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'BadRequest ACCESS LOG The token is not valid ');
    }
}, $auth_ADM);


$api->get('/users', function () use ($dbinfo) {
    $service = new UsersManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getUsersInfo();
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError COMPETENCES');
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_ADM);


$api->get('/users/{}', function ($id) use ($dbinfo) {
    $headers = getallheaders();
    if (!array_key_exists("Authorization", $headers))
        return Response::makeJSONResponse(401, "Unauthorized");

    $auth = new AuthorizationService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        if (!$auth->isAuthorizated($headers["Authorization"], ['ADM']) and !$auth->matchTokenWithID($headers["Authorization"], $id))
            return Response::makeJSONResponse(403, "Unauthorized");
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500);
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'The token is not valid ');
    }
    $service = new UsersManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getUserInfo($id);
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
});


$api->post('/users', function () use ($dbinfo) {
    $post = validatePost(
        file_get_contents("php://input"),
        'name',
        'surname',
        'birthdate',
        'phonenumber',
        'email',
        'userid',
        'password',
        'role'
    );
    if ($post == null) {
        return Response::makeJSONResponse(400, 'JSON is not valid!');
    }
    if ($post['role'] == 'MNT' and !array_key_exists("competences", $post))
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new UsersManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->addUser($post);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_ADM);


$api->delete('/users/{id}', function ($id) use ($dbinfo) {
    $service = new UsersManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->deleteUser($id);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_ADM);


$api->put('/users/{id}', function ($id) use ($dbinfo) {
    $post = validatePost(
        file_get_contents("php://input"),
        'name',
        'surname',
        'birthdate',
        'phonenumber',
        'email',
        'password'
    );
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new UsersManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->editUser($id, $post);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_ADM);


$api->get('/maintainers?availfromday&availtoday&availfromhour&availtohour', function () use ($dbinfo) {
    if ($this->hasParams() and !$this->numParams() === 4)
        return Response::makeJSONResponse(400);

    $service = new MaintainerManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = null;
        if ($this->numParams() === 4)
            $data = $service->getMaintainersUnavailabilitiesRange($this->getParam('availfromday'), $this->getParam('availtoday'), $this->getParam('availfromhour'), $this->getParam('availtohour'));
        else
            $data = $service->getMaintainersInfo();
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError get Maintainers');
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, 'fdssdf');
    }
}, $auth_PLN);


$api->get('/maintainers/{id}', function ($id) use ($dbinfo) {
    $service = new MaintainerManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getMaintainerInfo($id);
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_PLN);


$api->get('/activities?verbose', function () use ($dbinfo) {
    $service = new ActivityManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = null;
        if ($this->getParam('verbose') === 'true')
            $data = $service->getActivitiesInfoVerbose();
        else
            $data = $service->getActivitiesInfo();
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Activities');
    } catch (WrongDataFormatException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_PLN);


$api->get('/activities/{activityid}?verbose', function ($activityid) use ($dbinfo) {
    $service = new ActivityManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = null;
        if ($this->getParam('verbose') === 'true')
            $data = $service->getActivityInfoVerbose($activityid);
        else
            $data = $service->getActivityInfo($activityid);
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Activities');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_PLN);


$api->post('/activities', function () use ($dbinfo) {
    $post = validatePost(
        file_get_contents("php://input"),
        'activityid',
        'description',
        'scheduledweek',
        'estimatedtime',
        'site',
        'typology',
        'procedure',
        'interruptible',
        'materials'
    );
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new ActivityManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->addActivity($post);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError addActivity');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_PLN);


$api->put('/activities/{activityid}', function ($activityid) use ($dbinfo) {
    $post = validatePost(
        file_get_contents("php://input"),
        'description',
        'scheduledweek',
        'estimatedtime',
        'site',
        'typology',
        'procedure',
        'interruptible',
        'materials'
    );
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new ActivityManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->editActivity($activityid, $post);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError addActivity');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_PLN);


$api->delete('/activities/{activityid}', function ($activityid) use ($dbinfo) {
    $service = new ActivityManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->deleteActivity($activityid);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError addActivity');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_PLN);

$api->post('/assignactivity/{id}', function ($activityid) use ($dbinfo) {
    $post = validatePost(
        file_get_contents("php://input"),
        'userid',
        'day',
        'starttime',
        'endtime'
    );
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new ActivityManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->addAssignmentActivity($activityid, $post);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError addActivity');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_PLN);


$api->delete('/assignactivity/{id}', function ($activityid) use ($dbinfo) {
    $service = new ActivityManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->deleteAssignmentActivity($activityid);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError addActivity');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_PLN);


$api->get('/competences', function () use ($dbinfo) {
    $service = new CompetencesManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getCompetences();
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError COMPETENCES');
    }
}, $auth_ADM_PLN_DBL);


$api->get('/competences/{competenceid}', function ($competenceid) use ($dbinfo) {
    $service = new CompetencesManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getCompetence($competenceid);
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError COMPETENCES');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_ADM_PLN_DBL);


$api->post('/competences', function () use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"),'competenceid','name');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new CompetencesManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->insertCompetence($post['competenceid'], $post['name']);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400);
    }
}, $auth_DBL);


$api->delete('/competences/{id}', function ($id) use ($dbinfo) {
    $service = new CompetencesManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->deleteCompetence($id);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Competences');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_DBL);


$api->put('/competences/{id}', function ($id) use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"),'name');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new CompetencesManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->editCompetence($id, $post['name']);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_DBL);


$api->get('/procedures?verbose', function () use ($dbinfo) {
    $service = new ProcedureManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = null;
        if ($this->getParam('verbose') === 'true')
            $data = $service->getProceduresVerbose();
        else
            $data = $service->getProcedures();
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Procedures');
    }
}, $auth_ADM_PLN_DBL);


$api->get('/procedures/{procedureid}', function ($procedureid) use ($dbinfo) {
    $service = new ProcedureManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getProcedure($procedureid);
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Procedures');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_ADM_PLN_DBL);


$api->post('/procedures', function () use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"), 'procedureid', 'description', 'competencesrequired');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new ProcedureManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->insertProcedure($post['procedureid'], $post);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400);
    }
}, $auth_ADM_DBL);


$api->delete('/procedures/{id}', function ($id) use ($dbinfo) {
    $service = new ProcedureManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->deleteProcedure($id);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Competences');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_ADM_DBL);


$api->put('/procedures/{id}', function ($id) use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"), 'description', 'competencesrequired');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new ProcedureManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->editProcedure($id, $post);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Procedures');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_ADM_DBL);


$api->get('/activitytypologies', function () use ($dbinfo) {
    $service = new ActivityTypologyManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getActivityTypologies();
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Activity Typologies');
    }
}, $auth_PLN_DBL);


$api->get('/activitytypologies/{id}', function ($activitytypologyid) use ($dbinfo) {
    $service = new ActivityTypologyManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getActivityTypology($activitytypologyid);
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Activity Typologies');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_PLN_DBL);


$api->post('/activitytypologies', function () use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"), 'typologyid', 'description');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new ActivityTypologyManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->insertActivityTypology($post['typologyid'], $post['description']);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Activity Typologies');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400);
    }
}, $auth_DBL);


$api->delete('/activitytypologies/{id}', function ($id) use ($dbinfo) {
    $service = new ActivityTypologyManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->deleteActivityTypology($id);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Activity Typologies');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_DBL);


$api->put('/activitytypologies/{id}', function ($id) use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"),'description');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new ActivityTypologyManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->editActivityTypology($id, $post['description']);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Activity Typologies');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_DBL);


$api->get('/materials', function () use ($dbinfo) {
    $service = new MaterialManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getMaterials();
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError materials');
    }
}, $auth_PLN_DBL);


$api->get('/materials/{materialid}', function ($materialid) use ($dbinfo) {
    $service = new MaterialManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getMaterial($materialid);
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError materials');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_PLN_DBL);


$api->post('/materials', function () use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"), 'materialid', 'name');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new MaterialManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->insertMaterial($post['materialid'], $post['name']);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError materials');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400);
    }
}, $auth_DBL);


$api->delete('/materials/{id}', function ($id) use ($dbinfo) {
    $service = new MaterialManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->deleteMaterial($id);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError materials');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_DBL);


$api->put('/materials/{id}', function ($id) use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"),'name');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new MaterialManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->editMaterial($id, $post['name']);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError materials');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_DBL);


$api->get('/sites', function () use ($dbinfo) {
    $service = new SiteManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getSites();
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Site');
    }
}, $auth_PLN_DBL);


$api->get('/sites/{siteid}', function ($siteid) use ($dbinfo) {
    $service = new SiteManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $data = $service->getSite($siteid);
        return Response::makeJSONResponse(200, $data);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Site');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_PLN_DBL);


$api->post('/sites', function () use ($dbinfo) {
    $post = validatePost(file_get_contents("php://input"),'siteid','area','department');
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new SiteManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->insertSite($post['siteid'], $post['area'], $post['department']);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Site');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, '');
    }
}, $auth_DBL);


$api->put('/sites/{id}', function ($id) use ($dbinfo) {
    $post = validatePost(
        file_get_contents("php://input"),
        'area',
        'department',
    );
    if ($post == null)
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new SiteManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->editSite($id, $post['area'], $post['department']);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Site');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_DBL);


$api->delete('/sites/{id}', function ($id) use ($dbinfo) {
    $service = new SiteManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->deleteSite($id);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError Site');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_DBL);


$api->post('/procedures/{procedureid}/SMP', function ($procedureid) use ($dbinfo) {
    if (!array_key_exists('file', $_FILES))
        return Response::makeJSONResponse(400, 'JSON is not valid!');

    $service = new ProcedureManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $filecontent = file_get_contents($_FILES['file']['tmp_name']);
        $service->insertSMPFile($procedureid, "$procedureid.$ext", $filecontent);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400, strval($e));
    }
}, $auth_ADM_DBL);


$api->get('/procedures/{procedureid}/SMP', function ($procedureid) use ($dbinfo) {
    $service = new ProcedureManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $file = $service->getSMPFile($procedureid);
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
        header('Content-Length: ' . filesize($file));
        readfile($file);
        return Response::makeFileResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400);
    }
}, $auth_ADM_PLN_DBL);


$api->delete('/procedures/{procedureid}/SMP', function ($procedureid) use ($dbinfo) {
    $service = new ProcedureManagementService($dbinfo['host'], $dbinfo['port'], $dbinfo['dbname'], $dbinfo['username'], $dbinfo['password']);
    try {
        $service->deleteSMPFile($procedureid);
        return Response::makeJSONResponse(200);
    } catch (ServerErrorException $e) {
        return Response::makeJSONResponse(500, 'ServerError USERS');
    } catch (WrongDataFormatException | WrongDataException $e) {
        return Response::makeJSONResponse(400);
    }
}, $auth_ADM_DBL);

/* Bringing the request in the right route */
$api->route($_SERVER);