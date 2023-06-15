<?php
/**
 * Author: Tomáš Souček, xsouce15
 */
    include '../db_operations.php';
    include '../utils.php';
    header('Content-Type: text/plain; charset=utf-8');

    if(isset($_REQUEST["creator"]))
    {
        $name = $_SESSION["username"];
        $result = get_data("SELECT * FROM team LEFT JOIN user_team ON user_team.team_id = team.team_id WHERE user_id = ".$_SESSION["user_id"]." AND creator = 1 AND `name`!='$name'");
        echo json_encode($result);
    }
   
    if(isset($_REQUEST["member"]))
    {
        $result = get_data("SELECT * FROM team LEFT JOIN user_team ON user_team.team_id = team.team_id WHERE user_id = ".$_SESSION["user_id"]." AND creator != 1");
        echo json_encode($result);
    }

    if(isset($_GET["edit"]))
    {
        $id = $_GET["edit"];
        $result["players"] = get_data("SELECT * FROM team LEFT JOIN user_team ON user_team.team_id = team.team_id LEFT JOIN users ON user_team.user_id = users.user_id WHERE team.team_id = ".$id." ORDER BY user_team.creator DESC");
        $result["team"] = get_data("SELECT * FROM team WHERE team_id = ".$id);
        if(empty($result["team"]))
        {
            echo '{"success": 0}';
            http_response_code(400);
            die();
        }
        $result["allPlayers"] = get_data("SELECT * FROM users WHERE admin != 1");

        $result["playersPending"] = get_data("SELECT * FROM team_invite LEFT JOIN users ON users.user_id = team_invite.user_id WHERE team_invite.status = 0 AND team_invite.team_id = ".$id);

        $help = get_data("SELECT * FROM user_team WHERE team_id = ".$id." AND user_id=".$_SESSION["user_id"]);

        $result["canEdit"] = empty($help) ? 0 :  $help[0]["creator"];

        echo json_encode($result);
    }

    if(isset($_POST["edit"]))
    {
        $sql = "UPDATE team SET ";
        $arr = $_POST;
        $players = array();

        //vrátí pole všech username hráčů v daném týmu
        foreach(array_keys($arr) as $user)
        {
            if(strpos($user, "p") !== false)
            {
                $players[] = $arr[$user];
            } 
        }

        if(isset($_POST["name"]))
        {
            $name = $_POST["name"];
            $sql .= "name = '".$name."' ";
        }
        if(isset($_POST["team_id"]))
        {
            $team_id = $_POST["team_id"];
        }
        if(isset($_FILES['picture']['name']))
        {
            $filename = "team".$team_id."-".time(); //time používám kvůli unikátnosti souborů
        
            /* Location */
            $imageFileType = pathinfo($_FILES["picture"]["name"],PATHINFO_EXTENSION);
            $imageFileType = strtolower($imageFileType);
            $location = "../upload/".$filename.".".$imageFileType;          
        
            /* Valid extensions */
            $valid_extensions = array("jpg","jpeg","png", "webp");
        
            /* Check file extension */
            if(in_array(strtolower($imageFileType), $valid_extensions)) {
                /* Upload file */
                if(move_uploaded_file($_FILES['picture']['tmp_name'],$location))
                {
                    $picture = substr($location, 3); //3 chary jsou '../' protože view je jinak zanořené než model
                    $sql .= ", logo = '".$picture."' ";
                }
                else
                {
                    echo '{"success": 0}';
                    exit;
                }
            }
        }

        //TODO SQL SORT BY creator aby první user byl vždy creator a tudíž disabled input
    
        $sql .= " WHERE team_id = ".$team_id;

        
        $errors = 0;
        //pokud bylo vše ok potvrdí frontendu úspěch
        if(idu_data($sql) == 1) //upate názvu týmu
        {
            for($i = 0; $i < count($players); $i++)
            {
                $sql = "SELECT * FROM users LEFT JOIN user_team ON users.user_id = user_team.user_id WHERE users.username = '".$players[$i]."' AND team_id = ".$team_id;
                
                $results = get_data($sql);
                if(empty($results))
                {
                    $user_id = get_data("SELECT user_id FROM users WHERE username = '".$players[$i]."'");
                    $sql = "INSERT INTO team_invite (user_id, team_id, status) SELECT ".$user_id[0]["user_id"].",".$team_id.",0 WHERE NOT EXISTS (SELECT * FROM team_invite WHERE user_id = ".$user_id[0]["user_id"]." AND team_id = ".$team_id." AND status = 0)";
                    if(idu_data($sql) != 1)//pokud se nepovedlo vložit nového hráče, zvýší chybovou proměnnou
                    {
                        $errors++;
                    }
                }
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
    
    if(isset($_POST["create"]))
    {
        if(isset($_POST["name"]))
        {
            $name = $_POST["name"];
        }

        $sql = "SELECT * FROM team WHERE name = '".$name."'";
        $query = get_data($sql);

        if(!empty($query) || $name == "")
        {
            echo '{"success": 0}';
            die();
        }

        $sql = "INSERT INTO team (name, logo) SELECT '".$name."','https://i.pinimg.com/originals/27/4f/9f/274f9fdab17c756a369fe0a5898ebea6.jpg' WHERE NOT EXISTS (SELECT * FROM team WHERE name = '".$name."');";
        //print($sql);
 
        if(idu_data($sql) == 1)
        {
            $result = get_data("SELECT team_id FROM team WHERE name = '".$name."'"); //vrátí id nově založeného týmu

            $sql = "INSERT INTO user_team (user_id, team_id, creator) VALUES (".$_SESSION["user_id"].",".$result[0]["team_id"].",1)";
            if(idu_data($sql) == 1)
            {
                echo '{"success": 1, "team_id" : '.$result[0]["team_id"].'}';
            }
            else
            {
                echo '{"success": 0}';
            }
        }
        else
        {
            echo '{"success": 0}';
        }  

    }

    if(isset($_GET["delete"]))
    {
        if(isset($_GET["username"]))
        {
            $username = $_GET["username"];
        }
        if(isset($_GET["team_id"]))
        {
            $team_id = $_GET["team_id"];
        }
        if(isset($_GET["type"]))
        {
            $type = $_GET["type"];
        }

        $user_id = get_data("SELECT user_id FROM users WHERE username = '".$username."'");

        if($type == "invite")
        {
            $sql = "DELETE FROM team_invite WHERE user_id = ".$user_id[0]["user_id"]." AND team_id = ".$team_id;
        }
        else
        {
            $sql = "DELETE FROM user_team WHERE user_id = ".$user_id[0]["user_id"]." AND team_id = ".$team_id;
        }

       
        if(idu_data($sql) == 1)
        {
            echo '{"success": 1}';
        }
        else
        {
            echo '{"success": 0}';
        }      
       
    }

    if(isset($_POST["deleteTeam"]))
    {
        if(isset($_POST["team_id"]))
        {
            $team_id = $_POST["team_id"];
        }

        $sql = "DELETE FROM team WHERE team_id = ".$team_id;
        if(idu_data($sql) == 1)
        {
            echo '{"success": 1}';
        }
        else
        {
            echo '{"success": 0}';
        }  
        
    }


    if(isset($_GET["leave"]))
    {
        if(isset($_GET["team_id"]))
        {
            $team_id = $_GET["team_id"];
        }

        $sql = "DELETE FROM user_team WHERE user_id = ".$_SESSION["user_id"]." AND team_id = ".$team_id;
        if(idu_data($sql) == 1)
        {
            echo '{"success": 1}';
        }
        else
        {
            echo '{"success": 0}';
        }  
        
       
    }
    //nice_array_print($result);   
?>