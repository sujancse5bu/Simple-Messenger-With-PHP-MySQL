<?php


include('database_connection.php');
session_start();

$query = "UPDATE friends SET is_accepted = 1 WHERE (req_sender = ? AND req_reciever = ? ) OR (req_sender = ? AND req_reciever = ? )";
$statement = $connect-> prepare($query);
$statement->execute([$_SESSION['user_id'], $_POST['to_user_id'], $_POST['to_user_id'], $_SESSION['user_id']]);



?>