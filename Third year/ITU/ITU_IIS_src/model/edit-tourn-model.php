<?php
    /**
     * Author: David Kocman, xkocma08
     */
    include "../db_operations.php";
    include "../utils.php";
    header('Content-Type: text/plain; charset=utf-8');
    
    if(isset($_POST["update"])){
        $turn_id = $_SESSION["turnaj"];
        $name = $_POST["name"];
        $sport= $_POST["sport"];
        $date=$_POST["date"];
        $date = date('Y-m-d',strtotime($date));
        $descr=$_POST["descr"];
        $num=$_POST["num"];
        $type=$_POST["type"];
        $size=$_POST["size"];

        $query = "UPDATE turnament SET `name`='$name', `sport`='$sport', `date`='$date 00:00:00.000000', `description`='$descr', `max_teams`='$num', `type`='$type', `size`='$size' WHERE `turnament_id`='$turn_id'";
        $pom = idu_data($query);
    }
    else{
        
        $turn_id = $_SESSION["turnaj"];
        $query = "SELECT * FROM turnament WHERE turnament_id=$turn_id";
        $zaznam = get_data($query);

        $zaznam[0]["date"] = date("Y-m-d", strtotime($zaznam[0]["date"] ));

        echo json_encode($zaznam);
    }
    
    if(isset($_POST["delete"])){
        $turn_id = $_SESSION["turnaj"];
        $query = "DELETE FROM turnament WHERE turnament_id=$turn_id";
        idu_data($query);
    }
    
?>