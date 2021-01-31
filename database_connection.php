<?php

//database_connection.php

$connect = new PDO("mysql:host=localhost;dbname=chat", "root", "");

date_default_timezone_set('Asia/Kolkata');

function fetch_user_last_activity($user_id, $connect)
{
 $query = "
 SELECT * FROM login_details 
 WHERE user_id = '$user_id' 
 ORDER BY last_activity DESC 
 LIMIT 1
 ";
 $statement = $connect->prepare($query);
 $statement->execute();
 $result = $statement->fetchAll();
 foreach($result as $row)
 {
  return $row['last_activity'];
 }
}

function fetch_user_chat_history($from_user_id, $to_user_id, $connect)
{
 $query = "SELECT * FROM chat_message 
 WHERE (from_user_id = '".$from_user_id."' AND to_user_id = '".$to_user_id."') 
 OR (from_user_id = '".$to_user_id."' AND to_user_id = '".$from_user_id."') 
 ORDER BY timestamp DESC";
 $statement = $connect->prepare($query);
 $statement->execute();
 $result = $statement->fetchAll();
 $output = '<ul class="list-unstyled">';
 foreach($result as $row)
 {
  $time_class = '';
  $msg_class = '';
  if($row["from_user_id"] == $from_user_id)
  {
   $time_class = 'time-left';
   $msg_class = 'msg-right';
  }
  else
  {
    $time_class = 'time-right';
    $msg_class = 'msg-left';
  }
  $output .= '
  <li style="border-bottom:1px dotted #ccc">
    <div class="msg '.$msg_class.'">
        <p>'.$row["chat_message"].'</p>
    </div>
    <small class='.$time_class.'>'.$row['timestamp'].'</small>
  </li>
  ';
 }
 $output .= '</ul>';
 $query = "
 UPDATE chat_message 
 SET status = '0' 
 WHERE from_user_id = '".$to_user_id."' 
 AND to_user_id = '".$from_user_id."' 
 AND status = '1'
 ";
 $statement = $connect->prepare($query);
 $statement->execute();
 return $output;
}

function get_user_name($user_id, $connect)
{
 $query = "SELECT username FROM users WHERE id = '$user_id'";
 $statement = $connect->prepare($query);
 $statement->execute();
 $result = $statement->fetchAll();
 foreach($result as $row)
 {
  return $row['username'];
 }
}

function count_unseen_message($from_user_id, $to_user_id, $connect)
{
 $query = "
 SELECT * FROM chat_message 
 WHERE from_user_id = '$from_user_id' 
 AND to_user_id = '$to_user_id' 
 AND status = '1'
 ";
 $statement = $connect->prepare($query);
 $statement->execute();
 $count = $statement->rowCount();
 $output = '';
 if($count > 0)
 {
  $output = '<span class="label label-success">'.$count.'</span>';
 }
 return $output;
}

function fetch_is_type_status($user_id, $to_user_id, $connect)
{
 $query = "
 SELECT is_type, is_type_to FROM login_details WHERE user_id = '".$user_id."' 
 ORDER BY last_activity DESC 
 LIMIT 1
 "; 
 $statement = $connect->prepare($query);
 $statement->execute();
 $result = $statement->fetchAll();
 $output = '';
 foreach($result as $row)
 {
  if($row["is_type"] == 'yes' && $row["is_type_to"] == $to_user_id)
  {
   $output = ' <small><em><span class="text-muted">Typing...</span></em></small>';
  }
 }
 return $output;
}









//  new code starts here 




$friends = '';

function fetch_user_friends($user_id, $data_mode, $connect) {
    $query = "SELECT req_sender FROM friends  WHERE req_reciever = ? && is_accepted = 1";
    $statement = $connect->prepare($query);
    $statement->execute([$user_id]);
    $result = $statement->fetchAll();
    $output = array();
    foreach($result as $row) {
        array_push($output, $row['req_sender']);
    }
    


    $query = "SELECT req_reciever FROM friends  WHERE req_sender = ? && is_accepted = 1";
    $statement = $connect->prepare($query);
    $statement->execute([$user_id]);
    $result = $statement->fetchAll();
    foreach($result as $row) {
        array_push($output, $row['req_reciever']);
    }



    global $friends;
    $friends = '';
    $output2 = '';

    for ( $i = 0; $i < count($output); ++$i) {
        if ($i == count($output) - 1) $output2 .= $output[$i];
        else $output2 .= $output[$i] . ", ";
    }

    $friends = $output2;

    $query = "SELECT id, username, email FROM users WHERE id IN (" . $output2 . ")";
    $statement = $connect->prepare($query);
    $statement->execute();
    if ($data_mode == 2) return $statement->rowCount();
    $result = $statement->fetchAll();
    $output3 = '';    
    $count = $statement->rowCount(); 
    if($count == 0) $output3 = '<li class="center-text">No results have found.</li>';
    foreach($result as $row) {
        $output3 .= '
            <li>
                <p class="friend-name"><strong>' . $row['username'] . '</strong></p>
                <div class="friend-desc">
                    <div class="email"><span>' . $row['email'] . '</span></div>
                    <div class="uf-btn">
                        <button class="btn btn-info" data-id="' . $row['id'] . '">Send Message</button>
                        <button class="btn btn-danger" data-id="' . $row['id'] . '">Unfriend</button>
                    </div>
                </div>
            </li>
        ';
    }

    if ($data_mode == 1) return $output3;
}

function fetch_user_friend_reqs($user_id, $data_mode, $connect) {
    $query = "SELECT id, username , email FROM users WHERE id IN  (SELECT req_sender FROM friends  WHERE req_reciever = ?  && is_accepted = 0)";
    $statement = $connect->prepare($query);
    $statement->execute([$user_id]);
    if ($data_mode == 2) return $statement->rowCount();
    $result = $statement->fetchAll();
    $output = '';    
    $count = $statement->rowCount();
    if($count == 0) $output = '<li class="center-text">No results have found.</li>';
    foreach($result as $row) {
        $output .= 
            '<li>
                <p class="friend-name"><strong>' . $row['username'] . '</strong></p>
                <div class="friend-desc">
                    <div class="email"><span>' . $row['email'] . '</span></div>
                    <div class="uf-btn">
                        <button class="btn btn-success" data-id="' . $row['id'] . '">Accept</button>
                        <button class="btn btn-danger" data-id="' . $row['id'] . '">Delete</button>
                    </div>
                </div>
            </li>';
    }
    return $output;
}




function fetch_find_friends($user_id, $connect) {
    $query = "SELECT req_sender FROM friends  WHERE req_reciever = ?";
    $statement = $connect->prepare($query);
    $statement->execute([$user_id]);
    $result = $statement->fetchAll();
    $output = array($user_id);
    foreach($result as $row) {
        array_push($output, $row['req_sender']);
    }
    


    $query = "SELECT req_reciever FROM friends  WHERE req_sender = ?";
    $statement = $connect->prepare($query);
    $statement->execute([$user_id]);
    $result = $statement->fetchAll();
    foreach($result as $row) {
        array_push($output, $row['req_reciever']);
    }


    $output2 = '';

    for ( $i = 0; $i < count($output); ++$i) {
        if ($i == count($output) - 1) $output2 .= $output[$i];
        else $output2 .= $output[$i] . ", ";
    }


    $query = "SELECT id, username, email FROM users WHERE id NOT IN (" . $output2 . ")";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output3 = '';    
    $count = $statement->rowCount(); 
    if($count == 0) $output3 = '<li class="center-text">No results have found.</li>';
    foreach($result as $row) {
        $output3 .= '
            <li>
                <p class="friend-name"><strong>' . $row['username'] . '</strong></p>
                <div class="friend-desc">
                    <div class="email"><span>' . $row['email'] . '</span></div>
                    <div class="uf-btn">
                        <button class="btn btn-info" data-id="' . $row['id'] . '">Send Friend Request</button>
                    </div>
                </div>
            </li>
        ';
    }

    return $output3;
}


function fetch_unread_msg_count($to_user_id, $connect) {
    $query = "SELECT DISTINCT from_user_id FROM chat_message WHERE to_user_id = '$to_user_id' 
    AND status = '1'";
    $statement = $connect->prepare($query);
    $statement->execute();
    $count = $statement->rowCount();
    return $count;
}


function fetch_friends_search($value, $key, $friends, $connect) {
    $get_column = '';
    if ($key == 'name') $get_column = 'username';
    else $get_column = 'email';
    $query = "SELECT id, username, email FROM users WHERE id IN (" . $friends . ") AND " . $get_column . " LIKE '%" . $value . "%'";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output3 = '';
    $count = $statement->rowCount(); 
    if($count == 0) $output3 = '<li class="center-text">No results have found.</li>';
    foreach($result as $row) {
        $output3 .= '
            <li>
                <p class="friend-name"><strong>' . $row['username'] . '</strong></p>
                <div class="friend-desc">
                    <div class="email"><span>' . $row['email'] . '</span></div>
                    <div class="uf-btn">
                        <button class="btn btn-info" data-id="' . $row['id'] . '">Send Message</button>
                        <button class="btn btn-danger" data-id="' . $row['id'] . '">Unfriend</button>
                    </div>
                </div>
            </li>
        ';
    }
    return $output3;
}

function fetch_friend_reqs_search($value, $key, $user_id, $connect) {
    $get_column = '';
    if ($key == 'name') $get_column = 'username';
    else $get_column = 'email';
    $query = "SELECT id, username , email FROM users WHERE id IN  (SELECT req_sender FROM friends  WHERE req_reciever = ?  AND is_accepted = 0) AND " . $get_column . " LIKE '%" . $value . "%'";
    $statement = $connect->prepare($query);
    $statement->execute([$user_id]);
    $result = $statement->fetchAll();
    $output = '';    
    $count = $statement->rowCount(); 
    if($count == 0) $output = '<li class="center-text">No results have found.</li>';
    foreach($result as $row) {
        $output .= 
            '<li>
                <p class="friend-name"><strong>' . $row['username'] . '</strong></p>
                <div class="friend-desc">
                    <div class="email"><span>' . $row['email'] . '</span></div>
                    <div class="uf-btn">
                        <button class="btn btn-success" data-id="' . $row['id'] . '">Accept</button>
                        <button class="btn btn-danger" data-id="' . $row['id'] . '">Delete</button>
                    </div>
                </div>
            </li>';
    }
    return $output;

}





function fetch_find_friends_search($value, $key, $user_id, $connect) {
    $get_column = '';
    if ($key == 'name') $get_column = 'username';
    else $get_column = 'email';

    $query = "SELECT req_sender FROM friends  WHERE req_reciever = ?";
    $statement = $connect->prepare($query);
    $statement->execute([$user_id]);
    $result = $statement->fetchAll();
    $output = array($user_id);
    foreach($result as $row) {
        array_push($output, $row['req_sender']);
    }
    $query = "SELECT req_reciever FROM friends  WHERE req_sender = ?";
    $statement = $connect->prepare($query);
    $statement->execute([$user_id]);
    $result = $statement->fetchAll();
    foreach($result as $row) {
        array_push($output, $row['req_reciever']);
    }

    $output2 = '';

    for ( $i = 0; $i < count($output); ++$i) {
        if ($i == count($output) - 1) $output2 .= $output[$i];
        else $output2 .= $output[$i] . ", ";
    }


    $query = "SELECT id, username, email FROM users WHERE id NOT IN (" . $output2 . ") AND " . $get_column . " LIKE '%" . $value . "%'";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output3 = '';    
    $count = $statement->rowCount(); 
    if($count == 0) $output3 = '<li class="center-text">No results have found.</li>';
    foreach($result as $row) {
        $output3 .= '
            <li>
                <p class="friend-name"><strong>' . $row['username'] . '</strong></p>
                <div class="friend-desc">
                    <div class="email"><span>' . $row['email'] . '</span></div>
                    <div class="uf-btn">
                        <button class="btn btn-info" data-id="' . $row['id'] . '">Send Friend Request</button>
                    </div>
                </div>
            </li>
        ';
    }

    return $output3;
}
    
?>