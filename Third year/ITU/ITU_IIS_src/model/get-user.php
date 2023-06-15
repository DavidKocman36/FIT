<?php
/**
 * Author: Tomáš Souček, xsouce15
 */
    include '../db_operations.php';
    include '../utils.php';
    header('Content-Type: text/plain; charset=utf-8');

    if($_SERVER['REQUEST_METHOD'] == "GET")
    {
        $result = get_data("SELECT * FROM users WHERE user_id = ".$_SESSION["user_id"]);
        $result[0]["birthdate"] = date('Y-m-d', strtotime($result[0]["birthdate"]));
        echo json_encode($result);
    }

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $sql = "UPDATE users SET ";

        if(isset($_POST["name"]))
        {
            $name = $_POST["name"];
            $sql .= "name = '".$name."' ";
        }
        if(isset($_POST["surname"]))
        {
            $surname = $_POST["surname"];
            $sql .= ", surname = '".$surname."' ";
        }
        if(isset($_POST["email"]))
        {
            $email = $_POST["email"];
            $sql .= ", email = '".$email."' ";
        }
        if(isset($_POST["phone"]))
        {
            $phone = $_POST["phone"];
            $sql .= ", phone = '".$phone."' ";
        }
        if(isset($_POST["birthdate"]))
        {
            $birthdate = $_POST["birthdate"];
            $sql .= ", birthdate = '".$birthdate."' ";
        }
        if(isset($_POST["school"]))
        {
            $school = $_POST["school"];
            $sql .= ", school = '".$school."' ";
        }
        if(isset($_POST["year"]))
        {
            $year = $_POST["year"];
            $sql .= ", year = '".$year."' ";
        }
         if(isset($_POST["faculty"]))
        {
            $faculty = $_POST["faculty"];
            $sql .= ", faculty = '".$faculty."' ";
        }
       
       

        if(isset($_FILES['picture']['name']))
        {
            $filename = "user".$_SESSION["user_id"]."-".time();
        
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
                    $sql .= ", picture = '".$picture."' ";
                }
                else
                {
                    echo '{"success": 0}';
                    exit;
                }
            }
        }
        $sql .= " WHERE user_id = ".$_SESSION["user_id"];

        //pokud bylo vše ok potvrdí frontendu úspěch
        if(idu_data($sql) == 1)
        {
            echo '{"success": 1}';
        }
        else
        {
            echo '{"success": 0}';
        }
    }
?>