<?php


include('database_connection.php');
session_start();
fetch_user_friends($_SESSION['user_id'], 3, $connect);

$query = "SELECT id, username, email FROM users WHERE id IN (" . $friends . ")";
$statement = $connect-> prepare($query);
$statement->execute();
$count = $statement->rowCount();
$result = $statement->fetchAll();
$output = 
'<table class = "table table-bordered table-striped" >
    <tr>
        <th width = "70%"> Username </td> 
        <th width = "20%"> Status </td> 
        <th width = "10%"> Action </td>
    </tr>
';



foreach($result as $row) {
    $status = '';
    $current_timestamp = strtotime(date("Y-m-d H:i:s") . '+ 30 minute - 10 second');
    $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
    $user_last_activity = fetch_user_last_activity($row['id'], $connect);
    if ($user_last_activity > $current_timestamp) {
        $status = '<span class="label label-success">Online</span>';
    } else {
        $status = '<span class="label label-danger">Offline</span>';
    }
    $output .= ' <tr>
        <td> '.$row['username'].' '.count_unseen_message($row['id'], $_SESSION['user_id'], $connect).' '.fetch_is_type_status($row['id'], $_SESSION['user_id'], $connect).'</td> 
        <td class="label-outer"> '.$status.' </td> 
        <td> <button type = "button" class = "btn btn-info btn-xs start_chat" data-touserid = "'.$row['id'].'" data-tousername = "'.$row['username'].'"> Start Chat </button></td>
        </tr>
    ';
}

if($count == 0) $output .= '<p class="center-text">No friend have found.</p>';
$output .= '</table>';

echo $output;

?>