<?php
/**
 * Author: David Kocman, xkocma08
 */
    include "../db_operations.php";
    header('Content-Type: text/plain; charset=utf-8');

    function get($acc){
        $sql="SELECT * FROM turnament WHERE accepted='$acc' AND date >= CURDATE()";
	    $zaznamy = get_data($sql);
        echo json_encode($zaznamy);
    }

    function delete($id){
        $sql="DELETE FROM turnament WHERE turnament_id=$id";
        idu_data($sql);
    }

    function accept($id){
        $sql="UPDATE turnament SET accepted=1 WHERE turnament_id=$id";
        idu_data($sql);
    }

    function detail($id){
        $sql="SELECT * FROM turnament WHERE turnament_id=$id";
        $detail = get_data($sql);
        echo json_encode($detail);
    }

    if(isset($_GET["get"])){
        if($_GET["get"] == "not-acc"){

            get(0);

        }
        elseif ($_GET["get"] == "acc") {
    
            get(1);
    
        }
    }
    elseif(isset($_GET["delete"])){

        delete($_GET["delete"]);

    }
    elseif(isset($_GET["accept"])){

        accept($_GET["accept"]);

    }
    elseif(isset($_POST["detail_pop"])){

        detail($_POST["detail_pop"]);

    }
    
?>