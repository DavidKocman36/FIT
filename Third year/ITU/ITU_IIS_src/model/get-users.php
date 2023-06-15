<?php
/**
 * Author: Adam Cologna, xcolog00
 */
    include '../db_operations.php';
    include '../utils.php';
    header('Content-Type: text/plain; charset=utf-8');

    if($_SERVER['REQUEST_METHOD'] == "GET")
    {	
		if(isset($_SESSION["new_user"])){
			$result = get_data("SELECT * FROM users WHERE username='".$_SESSION["new_user"]."'");
            $user = $_SESSION["new_user"];
            $stats = get_data("SELECT match_id, users.username ,team.name, result1, result2 FROM match_ JOIN team ON match_.id_team1=team.team_id JOIN user_team ON user_team.team_id=team.team_id JOIN users ON users.user_id=user_team.user_id WHERE finished=1 AND users.username='$user'");
            $cnt = get_data("SELECT COUNT(DISTINCT match_.turnament_id) AS cnt FROM match_ JOIN team ON match_.id_team1=team.team_id JOIN user_team ON user_team.team_id=team.team_id JOIN users ON users.user_id=user_team.user_id WHERE finished=1 AND users.username='$user'");

            $result = array_merge($result, $cnt ,$stats);
		}
        $result[0]["birthdate"] = date('Y-m-d', strtotime($result[0]["birthdate"]));
        echo json_encode($result);
    }

    if(isset($_POST["Username"]))
    {	
		$username = $_POST['Username'];
		echo '{"username": "'.$username.'", "success": "ok"}';
		$_SESSION["new_user"] = $username;
		//exit();
		
    }

	if(isset($_POST["get-select"])){
		$result = get_data("SELECT name, surname, username FROM users WHERE admin = 0");
        echo json_encode($result);
	}
?>