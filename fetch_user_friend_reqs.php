<?php


include('database_connection.php');

session_start();


echo fetch_user_friend_reqs($_SESSION['user_id'], $_POST['data_mode'], $connect);

?>