<?php
/**
 * Author: Tomáš Souček, xsouce15
 */
    include '../db_operations.php';
    include '../utils.php';
    header('Content-Type: text/plain; charset=utf-8');


    if(isset($_POST["status"]) && isset($_POST["team_id"]))
    {
        $errors = 0;
        $status = $_POST["status"];
        $team_id = $_POST["team_id"];
        $sql = "UPDATE team_invite SET status = ".$status." WHERE user_id = ".$_SESSION["user_id"]." AND team_id = ".$team_id;
        if(idu_data($sql) != 1)
        {
            $errors++;
        }
        /*else if($status == -1)
        {
            $sql = "DELETE FROM team_invite WHERE WHERE user_id = ".$_SESSION["user_id"]." AND team_id = ".$team_id." AND status = ".$status;
            
            if(idu_data($sql) != 1)
            {
                $errors++;
            }
        }*/
        else if($status == 1)
        {
            $sql = "INSERT INTO user_team (user_id,team_id, creator) SELECT ".$_SESSION["user_id"].",".$team_id.",0 WHERE NOT EXISTS (SELECT * FROM user_team WHERE user_id = ".$_SESSION["user_id"]." AND team_id = ".$team_id.")";
          
            if(idu_data($sql) != 1)
            {
                $errors++;
            }
        }
    
        if($errors == 0)
        {
            echo '{"success": 1}';
        }
        else
        {
            echo '{"success": 0}';
        }
    }

   
  