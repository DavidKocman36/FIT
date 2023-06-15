<?php
/**
 * Author: Tomáš Souček, xsouce15
 */
    include '../db_operations.php';
    include '../utils.php';
	header('Content-Type: text/plain; charset=utf-8');

    if($_SERVER['REQUEST_METHOD'] == "GET")
    {	
		if(isset($_SESSION["current_team"])){
			$result = get_data("SELECT * FROM team WHERE team_id='".$_SESSION["current_team"]."'");
			$team = $_SESSION["current_team"];
			$matches = get_data("SELECT DISTINCT match_id ,team.name, result1, result2 FROM match_ JOIN team ON match_.id_team1=team.team_id JOIN user_team ON user_team.team_id=team.team_id WHERE finished=1 AND team.team_id = $team");
			$cnt = get_data("SELECT COUNT(DISTINCT match_.turnament_id) AS cnt FROM match_ JOIN team ON match_.id_team1=team.team_id JOIN user_team ON user_team.team_id=team.team_id WHERE finished=1 AND team.team_id=$team");

			$result = array_merge($result, $cnt ,$matches);
		}
        echo json_encode($result);
		
    }

    if(isset($_POST["Team_id"]))
    {	
		$team_id = $_POST['Team_id'];
		echo '{"team_id": "'.$team_id.'", "success": "ok"}';
		$_SESSION["current_team"] = $team_id;
		//exit();
		
    }

	if(isset($_POST["get-select"]))
	{
		$result = get_data("SELECT DISTINCT(team_id), name FROM team NATURAL JOIN user_team WHERE team_id NOT IN ( select team_id from user_team group by team_id having count(team_id) < 2 )");
        echo json_encode($result);
	}

	if(isset($_POST["members"]))
	{
		$team_id = $_POST["current_team"];
		//echo '{"team_id": "'.$team_id.'", "success": "ok", "members": "yes"}';
		$sql = "SELECT name, surname FROM user_team natural JOIN users WHERE team_id='".$_SESSION["current_team"]."'";
		$response = get_data($sql);
		echo json_encode($response);
	}
?>