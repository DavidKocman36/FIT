<?php
/**
 * Author: Adam Cologna, xcolog00
 */
function connect()
{
	//moznost individualne upravit 
	$servername = "localhost";
	$username = "root";
	$password = "123";

	$conn = mysqli_connect($servername, $username, $password,"iis"); //todo

			
	if (!$conn)
	{ 
		print("Connection failed: " . mysqli_connect_error());
	}
		
	mysqli_query($conn,"SET CHARACTER SET utf8");
	return $conn;
}


function get_data($sql)
{	
	$conn = connect();
	$query = mysqli_query($conn, $sql);

	$zaznamy = array();
	while($row = mysqli_fetch_assoc($query)) 
	{
		$zaznamy[] = $row;
	}
	return $zaznamy;
}

function idu_data($sql) //insert, delete or update data
{
	$conn = connect();
	
	if (!mysqli_query($conn, $sql))
	{
		return 0;
	}
	return 1;
}

?>
