<?php
/**
 * Author: David Kocman, xkocma08
 */
    include '../db_operations.php';
    include '../utils.php';
    header('Content-Type: text/plain; charset=utf-8');
    

    if(isset($_REQUEST["creator"]))
    {
		
        if(isset($_SESSION["username"])){
            $user = $_SESSION["username"];
            $query = get_data("SELECT user_id FROM users WHERE username='$user'");
            $user_id = $query[0]["user_id"];
			if(isset($_REQUEST["sort"]))
			{	
				$sort_value = $_REQUEST["sort"];
				$result = get_data("SELECT * FROM turnament WHERE `creator`='$user_id' AND date >= CURDATE() AND sport = '$sort_value'");
			}
			else{
				$result = get_data("SELECT * FROM turnament WHERE `creator`='$user_id' AND date >= CURDATE()");
			}
            
        }else{
            $result = get_data("SELECT * FROM turnament WHERE `creator`=-1");
        }
        
        for($i = 0; $i < count($result); $i++)
        {
            $currentCount = get_data("SELECT COUNT(*) AS currentCount FROM `team_turn` LEFT JOIN turnament ON turnament.turnament_id = team_turn.tournament_id WHERE team_turn.accepted=1 AND turnament.turnament_id = ".$result[$i]["turnament_id"]);
            $result[$i]["currentCount"] = $currentCount[0]["currentCount"];
            $result[$i]["date"] = date('d. m. Y',strtotime($result[$i]["date"]));
        }

        echo json_encode($result);
    }

    if(isset($_REQUEST["others"]))
    {
        if(isset($_SESSION["username"])){
            $user = $_SESSION["username"];
            $user_id = $_SESSION["user_id"];
			if(isset($_REQUEST["sort"]))
			{
				$sort_value = $_REQUEST["sort"];
				$result = get_data("SELECT DISTINCT turnament_id, turnament.name, sport, type, turnament.date, max_teams, size FROM turnament LEFT JOIN team_turn ON turnament.turnament_id=team_turn.tournament_id LEFT JOIN team ON team.team_id=team_turn.team_id LEFT JOIN user_team ON team.team_id=user_team.team_id LEFT JOIN users ON users.user_id=user_team.user_id WHERE turnament.creator!='$user_id' AND turnament.sport = '$sort_value' AND turnament.date >= CURDATE() AND turnament.accepted=1 AND turnament.turnament_id NOT IN (SELECT turnament_id FROM turnament LEFT JOIN team_turn ON turnament.turnament_id=team_turn.tournament_id LEFT JOIN team ON team.team_id=team_turn.team_id LEFT JOIN user_team ON team.team_id=user_team.team_id LEFT JOIN users ON users.user_id=user_team.user_id WHERE (users.user_id='$user_id'));");
			}
            else
			{
				 $result = get_data("SELECT DISTINCT turnament_id, turnament.name, sport, type, turnament.date, max_teams, size FROM turnament LEFT JOIN team_turn ON turnament.turnament_id=team_turn.tournament_id LEFT JOIN team ON team.team_id=team_turn.team_id LEFT JOIN user_team ON team.team_id=user_team.team_id LEFT JOIN users ON users.user_id=user_team.user_id WHERE turnament.creator!='$user_id' AND turnament.date >= CURDATE() AND turnament.accepted=1 AND turnament.turnament_id NOT IN (SELECT turnament_id FROM turnament LEFT JOIN team_turn ON turnament.turnament_id=team_turn.tournament_id LEFT JOIN team ON team.team_id=team_turn.team_id LEFT JOIN user_team ON team.team_id=user_team.team_id LEFT JOIN users ON users.user_id=user_team.user_id WHERE (users.user_id='$user_id'));");
			}
           
        }
        else{
			if(isset($_REQUEST["sort"]))
			{
				$sort_value = $_REQUEST["sort"];
				$result = get_data("SELECT * FROM turnament WHERE accepted=1 AND sport = '$sort_value'");
			}
			else
			{
				 $result = get_data("SELECT * FROM turnament WHERE accepted=1");
			}	
        }

        for($i = 0; $i < count($result); $i++)
        {
            $currentCount = get_data("SELECT COUNT(*) AS currentCount FROM `team_turn` LEFT JOIN turnament ON turnament.turnament_id = team_turn.tournament_id WHERE team_turn.accepted=1 AND turnament.turnament_id = ".$result[$i]["turnament_id"]);
            $result[$i]["currentCount"] = $currentCount[0]["currentCount"];
            $result[$i]["date"] = date('d. m. Y',strtotime($result[$i]["date"]));
        }
        
        echo json_encode($result);
    }
    if(isset($_REQUEST["added"])){
        if(isset($_SESSION["username"])){
            $user = $_SESSION["username"];
            $user_id = $_SESSION["user_id"];
            
			if(isset($_REQUEST["sort"]))
			{
				$sort_value = $_REQUEST["sort"];
				$result = get_data("SELECT DISTINCT turnament.name, turnament.type, turnament.sport, turnament.max_teams, turnament.date, turnament.turnament_id, turnament.size, team_turn.accepted FROM turnament LEFT JOIN team_turn ON turnament.turnament_id=team_turn.tournament_id LEFT JOIN team ON team.team_id=team_turn.team_id LEFT JOIN user_team ON team.team_id=user_team.team_id LEFT JOIN users ON users.user_id=user_team.user_id WHERE turnament.creator!='$user_id' AND turnament.date >= CURDATE() AND turnament.accepted=1 AND turnament.sport = '$sort_value' AND users.user_id='$user_id';");
			}
			else{
				$result = get_data("SELECT DISTINCT turnament.name, turnament.type, turnament.sport, turnament.max_teams, turnament.date, turnament.turnament_id, turnament.size, team_turn.accepted FROM turnament LEFT JOIN team_turn ON turnament.turnament_id=team_turn.tournament_id LEFT JOIN team ON team.team_id=team_turn.team_id LEFT JOIN user_team ON team.team_id=user_team.team_id LEFT JOIN users ON users.user_id=user_team.user_id WHERE turnament.creator!='$user_id' AND turnament.date >= CURDATE() AND turnament.accepted=1 AND users.user_id='$user_id'");
			}
            
            for($i = 0; $i < count($result); $i++)
            {
                $currentCount = get_data("SELECT COUNT(*) AS currentCount FROM `team_turn` LEFT JOIN turnament ON turnament.turnament_id = team_turn.tournament_id WHERE team_turn.accepted=1 AND turnament.turnament_id = ".$result[$i]["turnament_id"]);
                $result[$i]["currentCount"] = $currentCount[0]["currentCount"];
                $result[$i]["date"] = date('d. m. Y',strtotime($result[$i]["date"]));
            }
            echo json_encode($result);
        }
        else{
            echo '{}';
        }
    }

    if(isset($_REQUEST["detail"])){
        $_SESSION["turnaj"] = $_POST["detail"];
    }

    if(isset($_REQUEST["join"])){
        if(!isset($_SESSION["username"])){
            echo '{"logged": "0"}';
            die();
        }
        $id = $_POST["join"];
        $type = $_POST["type"];
        $username = $_SESSION["username"];
        if($type == 1){
            //udelej tym se jmenem uzivatele
            $query = "SELECT * FROM team WHERE `name`='$username'";
            $result = get_data($query);
            if(count($result) == 0){
                
                $user = get_data("SELECT user_id ,username, picture FROM users WHERE username='$username'");
                $name = $user[0]["username"];
                $picture = $user[0]["picture"];
                $user_id = $user[0]["user_id"];

                idu_data("INSERT INTO team (`team_id`, `name`, `logo`) VALUES (NULL , '$name' , '$picture')");
                
                $team_id = get_data("SELECT team_id FROM team WHERE `name`='$name'");
                $team_id_val = $team_id[0]["team_id"];
                $pom1= idu_data("INSERT INTO user_team (`usr_tm_id`, `user_id`, `team_id`, `creator`) VALUES (NULL , '$user_id' , '$team_id_val', 1)");

                $pom2 = idu_data("INSERT INTO team_turn (`tm_trn_id`, `team_id`, `tournament_id`, `accepted`) VALUES (NULL, $team_id_val, $id, 0)");
            }
            else{
                
                $name = $_SESSION["username"];
                $team_id = get_data("SELECT team_id FROM team WHERE `name`='$name'");
                $team_id_val = $team_id[0]["team_id"];

                $pom3 = idu_data("INSERT INTO team_turn (`tm_trn_id`, `team_id`, `tournament_id`, `accepted`) VALUES (NULL, $team_id_val, $id, 0)");
                print($pom3);
            }
        }
        else{
            
            $team_id = $_POST["join"];  
            $tourn_id =  $_POST["tourn_id"];
            idu_data("INSERT INTO team_turn (`tm_trn_id`, `team_id`, `tournament_id`, `accepted`) VALUES (NULL, $team_id, $tourn_id, 0)");

            echo '{"success": "1"}';
        }
    }
    
    if(isset($_REQUEST["teams"])){
        if(isset($_SESSION["user_id"])){
            $user_id = $_SESSION["user_id"];
            $name = $_SESSION["username"];
            $query = "SELECT * FROM team NATURAL JOIN user_team WHERE user_id=$user_id AND creator=1 AND `name`!='$name'";
            $result = get_data($query);
            echo json_encode($result);
        }
        else{
            echo '{"logged": "0"}';
            die();
        }
    }

    
    if(isset($_REQUEST["leave"])){
        $turn_id = $_POST["leave"];
        $user = $_SESSION["user_id"];

        $check = get_data("SELECT DISTINCT team.name, turnament.turnament_id, user_team.creator FROM turnament LEFT JOIN team_turn ON turnament.turnament_id=team_turn.tournament_id LEFT JOIN team ON team.team_id=team_turn.team_id LEFT JOIN user_team ON team.team_id=user_team.team_id LEFT JOIN users ON users.user_id=user_team.user_id WHERE turnament.creator!=$user AND turnament.date >= CURDATE() AND turnament.accepted=1 AND users.user_id=$user AND turnament.turnament_id=$turn_id;");
        if($check[0]["creator"] == 0){
            echo '{"error": "left"}';
            die();
        }

        $query = "DELETE FROM team_turn WHERE tournament_id=$turn_id";
        idu_data($query);

        echo '{"success": "left"}';
    }
    /**
     * Author: Tomáš Souček, xsouce15
     */
    if(isset($_GET["matchup"]))
    {
       
        $tournament_id = $_SESSION["turnaj"];
        //$tournament_id = 1;
        $sql = "SELECT * FROM match_ WHERE turnament_id = ".$tournament_id;
        if(isset($_GET["finished"]))
        {
            $sql .= " AND finished = ".$_GET["finished"];
        }
        $result = get_data($sql);
        for($i = 0; $i < count($result); $i++)
        {
            $team = get_data("SELECT * FROM team WHERE team_id = ".$result[$i]["id_team1"]);
            $result[$i]["team1_name"] = $team[0]["name"];
            $team = get_data("SELECT * FROM team WHERE team_id = ".$result[$i]["id_team2"]);
            $result[$i]["team2_name"] = $team[0]["name"];
        }
        //nice_array_print($result);
        echo json_encode($result);
    }
    /**
     * Author: Adam Cologna, xcolog00
     */
    if(isset($_REQUEST["results"]))
    {
        $tournament_id = $_SESSION["turnaj"];
        
        $results = [];
        $errors = 0;
        $arr = array_values($_POST); //přejmenování indexů na čísla

        $keys = array_keys($_POST); //hodnoty jsou id týmů
        for($i = 0; $i < count($keys); $i++)
        {
            $help = explode("&team", $keys[$i]);
            $keys[$i] = $help[1];

        }
        
        $index = 0;
        for($i = 0; $i < count($arr); $i += 2)
        {      
            $results[$index]["result1"] = $arr[$i];
            $results[$index]["result2"] = $arr[$i+1];
            $results[$index]["team1"] = $keys[$i];
            $results[$index]["team2"] = $keys[$i+1];
            $index++;
        }
        
        for($i = 0; $i < count($results); $i++)
        {
            if($results[$i]["result1"] != $results[$i]["result2"])
            {
                $sql = "UPDATE match_ SET result1 = ".$results[$i]["result1"].", result2 = ".$results[$i]["result2"]." , finished = 1 WHERE turnament_id = ".$tournament_id." AND id_team1 = ".$results[$i]["team1"]." AND id_team2 = ".$results[$i]["team2"];

                if(idu_data($sql) != 1)
                {
                    $errors++;
                    break;
                }
            }
            
        }  
        if($errors <= 0)
        {
            echo '{"success": 1}';
        }
        else
        {
            echo '{"success": 0}';
        }        
    }
    /**
     * Author: Tomáš Souček, xsouce15
     */

    if(isset($_REQUEST["permissions"]))
    {
        $tournament_id = $_SESSION["turnaj"];
        $insertResults = 1;
        $createMatchup = 1;

        $results = get_data("SELECT * FROM match_ WHERE finished = 0 AND turnament_id = ".$tournament_id);
        if(empty($results))
        {
            $insertResults = 0;
        }

        $results = get_data("SELECT matchup_generated FROM turnament WHERE turnament_id = ".$tournament_id);
        if($results[0]["matchup_generated"] == 1)
        {
            $createMatchup = 0;
        }
        echo '{"insertResults": '.$insertResults.', "createMatchup" : '.$createMatchup.'}';
    }


    if(isset($_REQUEST["create_tourn"])){
        if(isset($_SESSION["username"])){
            echo '{"good": "1"}';
        }
        else{
            echo '{"not-logged": "1"}';
        }
    }
    /**
     * Author: David Kocman, xkocma08
     */

    if(isset($_REQUEST["get-tourn"])){
        if(isset($_SESSION["user_id"])){
            $tourn_id = $_SESSION["turnaj"];
            $id = $_SESSION["user_id"];

            $join = get_data("SELECT users.username FROM team_turn NATURAL JOIN team JOIN user_team ON user_team.team_id=team.team_id JOIN users ON users.user_id=user_team.user_id WHERE users.user_id=$id AND team_turn.tournament_id=$tourn_id");
            if(empty($join)){
                $joined = array(array('joined' => 0));
            }
            else{
                $joined = array(array('joined' => 1));
            }
            
            $usr = get_data("SELECT user_id FROM users WHERE user_id=$id");

            //turnaj
            $tourn = get_data("SELECT * FROM turnament WHERE turnament_id=$tourn_id");

            //creator
            $user_id = $tourn[0]["creator"];
            $user = get_data("SELECT * FROM users WHERE user_id=$user_id");

            //teams
            $teams = get_data("SELECT * FROM team_turn NATURAL JOIN team WHERE tournament_id=$tourn_id AND accepted=1");

            $result = array_merge($usr, $tourn, $user, $joined , $teams);

            echo json_encode($result);
        }
        else{
            $tourn_id = $_SESSION["turnaj"];
            
            $usr = array(array("user_id" => "none"));

            $joined = array(array('joined' => 1)); //redundant

            //turnaj
            $tourn = get_data("SELECT * FROM turnament WHERE turnament_id=$tourn_id");

            //creator
            $user_id = $tourn[0]["creator"];
            $user = get_data("SELECT * FROM users WHERE user_id=$user_id");

            //teams
            $teams = get_data("SELECT * FROM team_turn NATURAL JOIN team WHERE tournament_id=$tourn_id AND accepted=1");

            $result = array_merge($usr, $tourn, $user, $joined , $teams);

            echo json_encode($result);
        }
        
    }
?>