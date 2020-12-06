<?php
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/DBConnection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/utils.php');

class ActivityManagementService{
    private $db;
    private $host = 'localhost';
    private $port = '5432';

    public function __construct()
    {
        $this->db = new Connection($this->host, $this->port, 'SEProject', 'se_user','team9user');
    }

    public function post($jsoninfo){        
        if (authorization($this->db)!='PLN')
            status(401, "Non sei autenticato per registrare un'attività");

        $postinfo = check_json_string($jsoninfo, array("activityid" => '/^([0-9]|[a-zA-Z]|_){1,20}$/')); 
        $foractivity = array(
            'activity_id' => $postinfo['activityid'],
            'description' => $postinfo['description'],
            'scheduled_week' => $postinfo['scheduledweek']
        );

        $result = $this->db->insert('activity', $foractivity);

        if($result)
            status(200, "attivita registrata correttamente");
        else
            status(403, "Non riesco a registrare l'attivita");
    }


    public function delete($activity_id){
        if (authorization($this->db)!='PLN')
            status(401, "Non sei autenticato per cancellare un'attivita.");

        $result = $this->db->select_delete('activity', array("activity_id"=>$activity_id));
        if ($result===false){
            status(500, "The activity doesn't exist.");
        }

        status(200, "The activity " . $activity_id . " has been removed!");
    }

    public function put($jsoninfo, $activity_id){
        if (authorization($this->db)!='PLN')
            status(401, "Non sei autenticato per registrare un'attività");

        $postinfo = check_json_string($jsoninfo, null); 
        $foractivity = array(
            'description' => $postinfo['description'],
            'scheduled_week' => $postinfo['scheduledweek']
        );
        
        $result=$this->db->select_update('activity',$foractivity,array('activity_id' => $activity_id));

        if($result)
            status(200, "The activity " . $activity_id . " has been updated! ");
        else
            status(403, "Non riesco ad aggiornare l'attivita");
    }

    public function get($activityid=null){
        if (authorization($this->db)!='PLN')
            status(401, "Non sei autenticato per ottenere un'attività");

            $sql = "SELECT activity_id as activityid, description, scheduled_week as scheduledweek, maintainer as assignedto
                    FROM activity LEFT JOIN assignment ON activity.activity_id=assignment.activity
                    ORDER BY scheduled_week ASC";
            $params = array();

            
        if ($activityid!=null){
            $sql = "SELECT activity_id as activityid, description, scheduled_week as scheduledweek, maintainer as assignedto
                    FROM activity LEFT JOIN assignment ON activity.activity_id=assignment.activity
                    WHERE activity_id = $1
                    ORDER BY scheduled_week ASC";
            $params['activity_id'] = $activityid;
        }
        
        $result = $this->db->queryParams($sql, $params);  

        if ($result == FALSE){
            status(403, 'No activity found');
        }
        if (@pg_num_rows($result)==0){
            status(403, 'No activity found');
        }

        $results = pg_fetch_all($result);
        

        if($activityid!=null){
            echo json_encode($results[0]);
        }else{
            echo json_encode($results);
        }
        status(200);
    }


    
    
}