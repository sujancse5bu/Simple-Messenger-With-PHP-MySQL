<?php


include('database_connection.php');

session_start();


echo fetch_find_friends($_SESSION['user_id'], $connect);

?>