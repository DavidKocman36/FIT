<?php 
/**
 * Author: David Kocman, xkocma08
 */
    session_start();
    include "../db_operations.php";

    $user = $_SESSION["username"];
    $query = get_data("SELECT user_id FROM users WHERE username='$user'");
    $user_id = $query[0]["user_id"];

    $name = $_POST["name"];
    $sport= $_POST["sport"];
    $date=$_POST["date"];
    $descr=$_POST["descr"];
    //$num=$_POST["num"];
    $acc=$_POST["acc"];
    $type=$_POST["type"];
    $max=$_POST["max"];
    $size=$_POST["size"];
    $creator = $user_id;

    //todo dodelat velikost tymu
    $query = "INSERT INTO `turnament` (`turnament_id`, `date`, `sport`, `type`, `description`, `min_teams`, `max_teams`, `accepted`, `squads`, `size`, `name`, `creator`) VALUES (NULL, '$date 00:00:00.000000', '$sport', '$type', '$descr', 2, '$max', '$acc', 0,'$size', '$name', '$creator');";
    idu_data($query);
?>