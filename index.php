<?php

include('database_connection.php');
session_start();


if(!isset($_SESSION['user_id']))
{
 header("location:login.php");
}


?>

<html>

<head>
  <title>Chat Application using PHP Ajax Jquery</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>

<body>
  <div class="container index-container">
  
    

    <h3 class="text-center top-title">Chat Application using PHP Ajax Jquery</h3>
    <span id="for_test"></span>

    
    <h4 class="manage-friend"><a href="friends.php"  class="manage-friends-btn">Manage Frineds</a></h4>
    

    <div class="table-responsive">
      <h4 class="text-center">All Conversations</h4>
      <p class="text-right">Hi - <strong> <?php echo $_SESSION['username'];  ?> </strong> - <a href="logout.php">Logout</a></p>
      <div id="user_details"></div>
      <div id="user_model_details"></div>
    </div>
  </div>
</body>

</html>

<script>
  $(document).ready(function () {


    var latest_to_user_id = 0


    fetch_user();

    send_message_check();


    setInterval(function () {
      update_last_activity();
      fetch_user();
      update_chat_history_data();
    }, 5000);

    function fetch_user() {
      $.ajax({
        url: "fetch_user.php",
        method: "POST",
        data: {
          to_user_id: latest_to_user_id
        },
        success: function (data) {
          $('#user_details').html(data);
        }
      })
    }

    function update_last_activity() {
      $.ajax({
        url: "update_last_activity.php",
        success: function () {

        }
      })
    }

    function make_chat_dialog_box(to_user_id, to_user_name) {
      var modal_content = '<div id="user_dialog_' + to_user_id + '" class="user_dialog" title="You have chat with ' + to_user_name + '">';
      modal_content +=
        '<div style="height:400px; border:1px solid #ccc; overflow-y: auto;" class="chat_history" data-touserid="' +
        to_user_id + '" id="chat_history_' + to_user_id + '">';
      modal_content += fetch_user_chat_history(to_user_id);
      modal_content += '</div>';
      modal_content += '<p class="is_type_modal"></p>';
      modal_content += '<div class="form-group">';
      modal_content += '<textarea name="chat_message_' + to_user_id + '" id="chat_message_' + to_user_id +
        '" class="form-control chat_message"></textarea>';
      modal_content += '</div><div class="form-group" align="right">';
      modal_content += '<button type="button" name="send_chat" id="' + to_user_id +
        '" class="btn btn-info send_chat">Send</button></div></div>';
      $('#user_model_details').html(modal_content);
    }


    function send_message_check() {
      $.ajax({
        url: 'send_message_check.php',
        success: function (data1) {
          if (data1 != 0) {

            $.ajax({
              url: 'fetch_single_username.php',
              method: 'POST',
              data: {
                user_id: data1
              },
              success: function(data2) {
                open_modal_and_render(data1, data2)
              }
            })
          }

        }
      })
    }

    function open_modal_and_render(to_user_id, to_user_name) {
      latest_to_user_id = to_user_id
      make_chat_dialog_box(to_user_id, to_user_name);
      $("#user_dialog_" + to_user_id).dialog({
        autoOpen: false,
        width: 400
      });
      $('#user_dialog_' + to_user_id).dialog('open');
    }

    $(document).on('click', '.start_chat', function () {
      var to_user_id = $(this).data('touserid');
      latest_to_user_id = to_user_id
      var to_user_name = $(this).data('tousername');
      make_chat_dialog_box(to_user_id, to_user_name);
      $("#user_dialog_" + to_user_id).dialog({
        autoOpen: false,
        width: 400
      });
      $('#user_dialog_' + to_user_id).dialog('open');
    });

  

    $(document).on('click', '.send_chat', function () {
      var to_user_id = $(this).attr('id');
      var chat_message = $('#chat_message_' + to_user_id).val();
      $.ajax({
        url: "insert_chat.php",
        method: "POST",
        data: {
          to_user_id: to_user_id,
          chat_message: chat_message
        },
        success: function (data) {
          $('#chat_message_' + to_user_id).val('');
          $('#chat_history_' + to_user_id).html(data);
        }
      })
    });

    function fetch_user_chat_history(to_user_id) {
      $.ajax({
        url: "fetch_user_chat_history.php",
        method: "POST",
        data: {
          to_user_id: to_user_id
        },
        success: function (data) {
          $('#chat_history_' + to_user_id).html(data);
        }
      })

      fetch_is_type_status(to_user_id);
    }

    function fetch_is_type_status(to_user_id) {
      $.ajax({
        url: "fetch_is_type_status.php",
        method: "POST",
        data: {
          to_user_id: to_user_id
        },
        success: function (data) {
          $('.is_type_modal').html(data);
        }
      })
    }

    function update_chat_history_data() {
      $('.chat_history').each(function () {
        var to_user_id = $(this).data('touserid');
        latest_to_user_id = to_user_id
        fetch_user_chat_history(to_user_id);
      });
    }

    $(document).on('click', '.ui-button-icon', function () {
      $('.user_dialog').dialog('destroy').remove();
    });


    var is_type = 'no';

    $(document).on('focus', '.chat_message', function () {
      is_type = 'yes';
      var to_user_id = $('.chat_history').data('touserid');
      update_type_status(to_user_id)
    });

    $(document).on('blur', '.chat_message', function () {
      is_type = 'no';
      var to_user_id = $('.chat_history').data('touserid');
      update_type_status(to_user_id)
    });

    function update_type_status(to_user_id) {
      
      $.ajax({
        url: "update_is_type_status.php",
        method: "POST",
        data: {
          is_type: is_type,
          to_user_id: to_user_id
        },
        success: function () {
          
        }
      })
    }

  });



</script>