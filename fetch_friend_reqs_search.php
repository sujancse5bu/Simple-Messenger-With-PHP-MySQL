<?php

include('database_connection.php');
session_start();
echo fetch_friend_reqs_search($_POST['search_value'], $_POST['search_key'], $_SESSION['user_id'], $connect);

?>

