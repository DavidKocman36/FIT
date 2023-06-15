<?php 
/**
 * Author: David Kocman, xkocma08
 */
    include "../utils.php";
    header('Content-Type: text/plain; charset=utf-8');

    $sql = "SELECT * FROM `turnament` WHERE DATE(`date`) >= DATE(CURDATE()) AND matchup_generated=1";
    $result = get_data($sql);

    for($i = 0; $i < count($result); $i++)
    {
        $result[$i]["date"] = date('d. m. Y',strtotime($result[$i]["date"]));
    }

    echo json_encode($result);

    if(isset($_REQUEST["detail"])){
        $_SESSION["turnaj"] = $_POST["id"];
    }
?>