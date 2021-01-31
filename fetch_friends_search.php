<?php

include('database_connection.php');
session_start();
$test = fetch_user_friends($_SESSION['user_id'], 1, $connect);
echo fetch_friends_search($_POST['search_value'], $_POST['search_key'], $friends, $connect);

?>