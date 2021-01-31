<?php

include('database_connection.php');
session_start();

$message = '';

if(isset($_SESSION['user_id'])) {
 header('location:index.php');
}

if(isset($_POST["login"]))
{
 $query = "SELECT * FROM users WHERE username = :username";
 $statement = $connect->prepare($query);
 $statement->execute(
    array(
      ':username' => $_POST["username"]
     )
  );
  $count = $statement->rowCount();
  
  if($count > 0)
 {
  $result = $statement->fetchAll();
    foreach($result as $row)
    {
      if($_POST["password"]==$row["password"]) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['send_message_id'] = 0;

        $sub_query = "UPDATE login_details SET last_activity = now() WHERE user_id = '".$row['id']."'";
        $statement = $connect->prepare($sub_query);
        $statement->execute();
        $_SESSION['login_details_id'] = $connect->lastInsertId();
        header("location:index.php");
      } else {
       $message = "<label>Wrong Username or Password</label>";
      }
    }
 }
 else {
  $message = "<label>Wrong Username or Password</label>";
 }
}

?>

<html>

<head>
  <title>Chat Application using PHP Ajax Jquery</title>
  <meta namespace="viewport" size="width=device-width, scale=1">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
  <div class="container log-in">
    <h3 class="center-text top-title">Chat Application using PHP Ajax Jquery</h3>
    <div class="panel-container">
      <div class="panel panel-default log-in-panel">
        <div class="panel-heading">Chat Application Login</div>
        <div class="panel-body">
          <form method="post">
            <p class="text-danger"><?php echo $message; ?></p>
            <div class="form-group">
              <label>Enter Username</label>
              <input type="text" name="username" class="form-control" required />
            </div>
            <div class="form-group">
              <label>Enter Password</label>
              <input type="password" name="password" class="form-control" required />
            </div>
            <div class="form-group">
              <input type="submit" name="login" class="btn btn-info" value="Login" />
            </div>
          </form>
        </div>
      </div>


      <div class="panel panel-default sign-up-panel">
        <div class="panel-heading">Chat Application Sign Up</div>
        <div class="panel-body">
           <h3>Haven't account yet!</h3>
           <p>Please sign-up here.</p>
           <a href="signup.php" class="btn btn-success"><h4>Sign Up</h4></a>
        </div>
      </div>
    </div>
    </div>
</body>

</html>