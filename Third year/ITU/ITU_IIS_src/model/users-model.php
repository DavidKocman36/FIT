<?php 
/**
 * Author: David Kocman, xkocma08
 */
    include '../db_operations.php';
    header('Content-Type: text/plain; charset=utf-8');
    
    function delete($id){
        $sql="DELETE FROM users WHERE user_id=$id";
        $ret = idu_data($sql);
        get_or_update();
    }

    function get_or_update(){
        $sql="SELECT * FROM users";
	    $zaznamy = get_data($sql);
        echo json_encode($zaznamy);
    }

    
    if(isset($_GET['delete'])) {
        delete($_GET['delete']);
    }
    elseif(isset($_POST['pass'])){
        $id = $_POST['pass'];
        $pass = $_POST['text'];
        $pass = md5($pass);
        $sql = "UPDATE users SET password='$pass' WHERE user_id=$id";
        $ret = idu_data($sql);
    }
    else{
        get_or_update();
    }
?>