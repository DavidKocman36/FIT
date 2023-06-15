<?php
/**
 * Author: David Kocman, xkocma08
 */
    include "../db_operations.php";

    $sql_usr="SELECT * FROM users";
	$zaznamy_usr = get_data($sql_usr);
    $usr = count($zaznamy_usr);

    $sql_tur_passed="SELECT * FROM turnament WHERE date<CURRENT_DATE()";
    $zaznamy_tur_passed = get_data($sql_tur_passed);
    $tur_p = count($zaznamy_tur_passed);

    $sql_tur_upcoming="SELECT * FROM turnament WHERE date>=CURRENT_DATE()";
    $zaznamy_tur_upcoming = get_data($sql_tur_upcoming);
    $tur_u = count($zaznamy_tur_upcoming);

    $sql_teams="SELECT * FROM team";
    $zaznamy_teams = get_data($sql_teams);
    $teams = count($zaznamy_teams);

    header('Content-Type: text/plain; charset=utf-8');
    echo '{"users": "'.$usr.'","turn_p": "'.$tur_p.'","turn_u": "'.$tur_u.'","teams": "'.$teams.'"}';
?>