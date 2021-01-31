<?php

include('database_connection.php');
session_start();

echo fetch_is_type_status( $_POST['to_user_id'], $_SESSION['user_id'], $connect)
?>