<?php

include('database_connection.php');
session_start();

$query = "INSERT INTO friends(req_sender, req_reciever, is_accepted) VALUES ( ?, ?, 0)";
$statement = $connect-> prepare($query);
$statement->execute([$_SESSION['user_id'], $_POST['to_user_id']]);

?>