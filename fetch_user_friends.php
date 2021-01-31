<?php


include('database_connection.php');

session_start();


echo fetch_user_friends($_SESSION['user_id'], $_POST['data_mode'], $connect);

?>