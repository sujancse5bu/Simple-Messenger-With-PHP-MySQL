<?php

include('database_connection.php');

if(isset($_POST["signup"])) {

    $username = $_POST["username"];
    $email = $_POST["email"];
    $pswd = $_POST["password"];

    $query = "INSERT INTO users (username, email, password) VALUES ( ?, ?, ?)"; 
    $statement = $connect->prepare($query);
    $statement->execute([$username, $email, $pswd]);
    

    $query = "SELECT MAX(id) from users"; 
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetch();

    session_start();
    $_SESSION['username'] = $username;
    $_SESSION['user_id'] = $result[0];
    $_SESSION['send_message_id'] = 0;


    $sub_query = "INSERT INTO login_details (user_id) VALUES ('".$result[0]."')";
    $statement = $connect->prepare($sub_query);
    $statement->execute();
    $_SESSION['login_details_id'] = $connect->lastInsertId();

    header("location: index.php");
}




?>


<html>

<head>
    <title>Chat Application - Sign Up</title>
    <meta namespace="viewport" size="width=device-width, scale=1">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
    <div class="container sign-up-container">
        <h3 class="text-center top-title">Chat Application using PHP Ajax Jquery</h3>
        <div class="panel panel-default">
            <div class="panel-heading">Chat Application Sign Up</div>
            <div class="panel-body">
                <form method="post">
                    <div class="form-group">
                        <label>Enter Username</label>
                        <input type="text" name="username" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label>Enter Email</label>
                        <input type="email" name="email" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label>Enter Password</label>
                        <input type="password" name="password" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <input type="submit" name="signup" class="btn btn-info" value="Sign Up" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>