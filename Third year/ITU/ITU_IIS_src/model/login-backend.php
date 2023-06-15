<?php 
/**
 * Author: David Kocman, xkocma08
 */
	session_start();
	include '../db_operations.php';
	header('Content-Type: text/plain; charset=utf-8');

	if(isset($_POST["logout-submit"])){
		if(isset($_SESSION["username"])){
			session_destroy();
			echo '{"logout": "done"}';
		}
	}
	if(isset($_POST["login-submit"])){

		$login_bool=1;
		

		if(isset($_POST["login-username"])){
			$login=trim($_POST["login-username"]);
			$sql = "SELECT * FROM `users` WHERE username='$login'";
			$db_data = get_data($sql);
			if(empty($db_data)){
				$login_bool=0;
				echo '{"chyba": "user"}';
			}
			else{
				$sql = "SELECT password, admin FROM `users` WHERE username='$login'";
				$pswd_db = get_data($sql);
				if($pswd_db[0]["password"]!=md5($_POST["login-password"])){
					$login_bool=0;
					echo '{"chyba": "user"}';
					
				}
				else{
					$_SESSION["username"] = $login;
					$_SESSION["user_id"] = $db_data[0]["user_id"];
					$admin = $pswd_db[0]["admin"];
					echo '{"success": "'.$login.'","admin": "'.$admin.'"}';
					exit();
				}
			}
		}
	}
?>