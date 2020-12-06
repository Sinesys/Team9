<?php

function status($code, $message = NULL, $die = true){
    if ($message != NULL){
        echo json_encode(['message' => $message]);
    }
    http_response_code($code);
    if ($die === true){
        die();
    }
};

/**
 * Compare a json string against a given collection of regex rules.
 * 
 * The function assume that the values of the json string are all of string type.
 *
 * @param string $jsonstring the json string to be checked.
 * @param array|null $rules associative array containg pairs in the form "key_json" => "regex_for_value".
 * All the keys must be present in the json string and all the rules must be matched.  
 * @param array|null $optional_rules associative array containg pairs in the form "key_json" => "regex_for_value".
 * All the rules must be matched, if the relative key is present in the given json string.
 * @return array|null The json string decoded into an associative array or null if the rules are not matched.
 */
function check_json_string($jsonstring, $rules, $optional_rules = null){
    $postinfo = json_decode($jsonstring, true);
    if ($postinfo === null){
        status(400, 'JSON is not valid!');
    }
    
    if ($rules == null){
        return $postinfo;
    }
    foreach($rules as $key=>$rule){
        $result = false;
        if (array_key_exists($key, $postinfo))
            $result = preg_match($rule, $postinfo[$key]);
        
        if ($result == false){
            status(400, "JSON is not valid! Check the '".$key."' field.");
        }
    }

    if ($optional_rules == null){
        return $postinfo;
    }
    foreach($optional_rules as $key=>$rule){
        if (!array_key_exists($key, $postinfo))
            continue;
        if (preg_match($rule, $postinfo[$key]) == false){
            status(400, "JSON is not valid! Check the '".$key."' field.");
        }
    }
    return $postinfo;
}

function authorization($db){
    $headers = getallheaders();
    if(!array_key_exists("Authorization", $headers))
        status(401, "Unauthorized");
   
    $token = getallheaders()["Authorization"];
    if(preg_match('/(.){1,50}/', $token) == false)
        status(401, "Unauthorized");

    if($db->select('authentication', array("token" => $token, "expires" => date( "Y-m-d H:i:s")), true))
        status(401, "Unauthorized");

    $db->query("DELETE FROM authentication WHERE expires <= CURRENT_TIMESTAMP(0)");
    $sql = "SELECT role
            FROM system_user, authentication
            WHERE (system_user.id=authentication.id AND 
                authentication.token = $1);";

    $result = $db->queryParams($sql, array ("token"=>"$token"));
    if ($result == FALSE){
        status(401, "Server error");
    }
    if (@pg_num_rows($result)==0){
        status(401, 'Server error');
    }

    $result = pg_fetch_assoc($result);
    
    return $result['role']; // ADM/PLN/MNT/DBL
}
?>