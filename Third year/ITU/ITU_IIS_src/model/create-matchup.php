<?php
/**
 * Author: Tomáš Souček, xsouce15
 */
    include '../db_operations.php';
    include '../utils.php';
    header('Content-Type: text/plain; charset=utf-8');

    if(isset($_GET["initial"]))
    {
        if(isset($_SESSION["turnaj"]))
        {
            $tournament_id = $_SESSION["turnaj"];
           
        }
        else
        {
            echo '{"success": 0}';
            die();
        }

        
    
        $errors = 0;

        //vygeneruje soupisku pouze pokud je přihlášený user creatorem turnaje
        $user = get_data("SELECT * FROM turnament WHERE creator = ".$_SESSION["user_id"]." AND turnament_id = ".$tournament_id);
        if(empty($user[0]))
        {
            echo '{"success": 0}';
            die();
        }
        
    

        $teams = get_data("SELECT * FROM team_turn WHERE tournament_id = ".$tournament_id." AND accepted = 1");
        //nice_array_print($teams);

        if(count($teams) % 2 != 0)
        {
            echo "error - není sudý počet týmů (maybe dát volný los?)";
            
        }
        shuffle($teams); //náhodné promíchání indexů
        //nice_array_print($teams);

    
        //dávám dohromady dva týmy, tudíž ať to iteruje po dvojicích
        for($i = 0; $i < count($teams) -1; $i += 2)
        {
            $sql = "INSERT INTO match_ (date,id_team1, id_team2, result1, result2, turnament_id, finished) VALUES (CURRENT_TIMESTAMP, ".$teams[$i]["team_id"].", ".$teams[$i+1]["team_id"].", 0, 0, ".$tournament_id.", 0)";
            if(idu_data($sql) != 1)
            {
                $errors++;
            }
        }
        if($errors == 0)
        {
            idu_data("UPDATE turnament SET matchup_generated = 1 WHERE turnament_id = ".$tournament_id);
            echo '{"success": 1}';
        }
        else
        {
            echo '{"success": 0}';
        }
    }
    
    if(isset($_GET["ongoing"]))
    {
        if(isset($_SESSION["turnaj"]))
        {
            $tournament_id = $_SESSION["turnaj"];
           
        }
        else
        {
            echo '{"success": 0}';
            die();
        }

        
    
        $errors = 0;

        //vygeneruje soupisku pouze pokud je přihlášený user creatorem turnaje
        $user = get_data("SELECT * FROM turnament WHERE creator = ".$_SESSION["user_id"]." AND turnament_id = ".$tournament_id);
        if(empty($user[0]))
        {
            echo '{"success": 0}';
            die();
        }
        
    

        $teams = get_data("SELECT * FROM match_ WHERE turnament_id = ".$tournament_id." AND finished = 1");
        //nice_array_print($teams);

        if(count($teams) % 2 != 0)
        {
             echo '{"success": 1}';
            die();
            echo "error - není sudý počet týmů (maybe dát volný los?)";
            
        }
        
        //nice_array_print($teams);

    
        //dávám dohromady dva týmy, tudíž ať to iteruje po dvojicích
        for($i = 0; $i < count($teams) -1; $i += 2)
        {
            $winner1 = $teams[$i]["result1"] > $teams[$i]["result2"] ? $teams[$i]["id_team1"] : $teams[$i]["id_team2"];
            $winner2 = $teams[$i+1]["result1"] > $teams[$i+1]["result2"] ? $teams[$i]["id_team1"] : $teams[$i+1]["id_team2"];
            $sql = "INSERT INTO match_ (date,id_team1, id_team2, result1, result2, turnament_id, finished) VALUES (CURRENT_TIMESTAMP, ".$winner1.", ".$winner2.", 0, 0, ".$tournament_id.", 0)";
            //echo $sql;
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
    
   
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        if(isset($_SESSION["turnaj"]))
        {
            $tournament_id = $_SESSION["turnaj"];   
        }
        else
        {
            echo '{"success": 0}';
            die();
        }
        //$tournament_id = 1;//odstranit todo
        
        $errors = 0;
        $teams = json_decode(stripslashes($_POST['data']));
        for($i = 0; $i < count($teams) -1; $i += 2)
        {
            $sql = "INSERT INTO match_ (date,id_team1, id_team2, result1, result2, turnament_id, finished) VALUES (CURRENT_TIMESTAMP, ".$teams[$i].", ".$teams[$i+1].", 0, 0, ".$tournament_id.", 0)";
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


