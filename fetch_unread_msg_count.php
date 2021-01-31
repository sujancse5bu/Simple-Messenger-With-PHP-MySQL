<?php

include('database_connection.php');

session_start();

echo fetch_unread_msg_count($_SESSION['user_id'], $connect);

?>