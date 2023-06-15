<?php
/**
 * Author: David Kocman, xkocma08
 */
    include "../db_operations.php";
    include "../utils.php";
    header('Content-Type: text/plain; charset=utf-8');

    if(isset($_REQUEST["create"])){
        $tourn_id = $_SESSION["turnaj"];
        
        $query = "SELECT * FROM team_turn NATURAL JOIN team WHERE tournament_id=$tourn_id AND accepted=0";
        $result = get_data($query);
        
        echo json_encode($result);
    }
    if(isset($_POST['acc_or_dec'])){
        $tourn_id = $_SESSION["turnaj"];

        $max = get_data("SELECT max_teams FROM turnament WHERE turnament_id=$tourn_id");
        $error = get_data("SELECT COUNT(*) AS currentCount FROM `team_turn` LEFT JOIN turnament ON turnament.turnament_id = team_turn.tournament_id WHERE team_turn.accepted=1 AND turnament.turnament_id = $tourn_id");
        
        if($error[0]["currentCount"] + 1 > $max[0]["max_teams"]){
            echo '{"update": "error"}';
            return;
        }
        
        $flag = $_POST['acc_or_dec'];
        $id = $_POST['id'];
        

        if($flag == 1){
            //acc
            $query = "UPDATE team_turn SET accepted=1 WHERE tm_trn_id=$id";
            idu_data($query);

            echo '{"update": "done"}';
        }
        else{
            //dec
            $query = "DELETE FROM team_turn WHERE tm_trn_id=$id";
            idu_data($query);

            echo '{"delete": "done"}';
        }
    }

    if(isset($_REQUEST["added"])){
        $tourn_id = $_SESSION["turnaj"];

        $query = "SELECT type, max_teams FROM turnament WHERE turnament_id=$tourn_id";
        $type = get_data($query);
        
        $query = "SELECT * FROM team_turn NATURAL JOIN team WHERE tournament_id=$tourn_id AND accepted=1";
        $result = get_data($query);

        $res = array_merge($type, $result);
        echo json_encode($res);   
    }

    if(isset($_REQUEST["detail"])){
        $tourn_id = $_SESSION["turnaj"];
        $query = "SELECT type, size FROM turnament WHERE turnament_id=$tourn_id";
        $type = get_data($query);

        $team_id = $_POST["id"];
        $query = "SELECT COUNT(*) AS cnt FROM user_team NATURAL JOIN users WHERE team_id=$team_id";
        $result = get_data($query);
        
        $res = array_merge($type, $result);
        echo json_encode($res);  
    }

    if(isset($_REQUEST["size"])){
        $tourn_id = $_SESSION["turnaj"];
        $query = "SELECT size,type FROM turnament WHERE turnament_id=$tourn_id";
        $result = get_data($query);
        
        echo json_encode($result);  
    }
    

?>