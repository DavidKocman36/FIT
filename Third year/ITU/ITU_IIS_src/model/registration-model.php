<?php
/**
 * Author: David Kocman, xkocma08
 */
  include "../utils.php";
  header('Content-Type: text/plain; charset=utf-8');
  
    //povinne
    $name = $_POST['Name'];
    $surname = $_POST['Surname'];
    $email = $_POST['Email'];
    $username = $_POST['Username'];
    $birthdate = $_POST['Date'];
    $phone = $_POST['Phone'];
    //
    if(isset($_POST['School'])){
      if(strlen( $_POST['School'] ) === 0){
        $school = "Žádná";
      }
      else{
        $school = $_POST['School'];
      }
      
    }
    if(isset($_POST['Faculty'])){
      if(strlen( $_POST['Faculty'] ) === 0){
        $faculty = "Žádná";
      }
      else{
        $faculty = $_POST['Faculty'];
      }
    }
    if(isset($_POST['Year'])){
      if($_POST['Year'] >= 1){
        $year = $_POST['Year'];
      }
      else{
        $year = 1;
      }
    }

    //povinne
    $password = $_POST['Password'];

    $sql_em = "SELECT * FROM users WHERE (email='$email')";
    $sql_us = "SELECT * FROM users WHERE (username='$username')";
    $sql_ph = "SELECT * FROM users WHERE (phone='$phone')";
    
    $query_em = get_data($sql_em);
    $query_ph = get_data($sql_ph);
    $query_us = get_data($sql_us);

    if(!empty($query_em)){
      echo '{"fail": "email"}';
    }
    elseif(!empty($query_us)){
      echo '{"fail": "username"}';
    }
    elseif(!empty($query_ph)){
      echo '{"fail": "phone"}';
    }
    else{
	    $_SESSION["username"] = $username;
      $sql = "INSERT INTO `users` (`user_id`, `username`, `name`, `surname`, `email`, `phone`, `birthdate`, `school`, `faculty`, `year`, `password`, `admin`, `picture`) VALUES (NULL, '$username', '$name', '$surname', '$email', '$phone', '$birthdate 00:00:00.000000', '$school', '$faculty', '$year', '$password', '0', 'https://media.istockphoto.com/vectors/male-profile-icon-white-on-the-blue-background-vector-id470100848?k=20&m=470100848&s=612x612&w=0&h=ZfWwz2F2E8ZyaYEhFjRdVExvLpcuZHUhrPG3jOEbUAk=');";
	    if(!idu_data($sql)){
        echo '{"fail": "insert"}';
        die();
      }
      $res = get_data("SELECT user_id FROM users WHERE username='$username'");
      $_SESSION["user_id"] = $res[0]["user_id"];
      echo '{"success": "inserted"}';
	  exit();
	
    }

    
?>