<?php

include('database_connection.php');

session_start();

$query = "UPDATE login_details SET is_type = '".$_POST["is_type"]."', is_type_to = '" .$_POST["to_user_id"]. "' WHERE user_id = '".$_SESSION["user_id"]."'
";

$statement = $connect->prepare($query);

$statement->execute();

?>
