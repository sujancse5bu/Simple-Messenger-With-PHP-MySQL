

<?php


include('database_connection.php');

session_start();

$_SESSION['send_message_id'] = 0;

echo get_user_name($_POST['user_id'], $connect);

?>