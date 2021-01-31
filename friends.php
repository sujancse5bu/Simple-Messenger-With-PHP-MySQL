<?php

include('database_connection.php');

session_start();

if(!isset($_SESSION['user_id']))
{
 header("location:login.php");
}

if (isset($_POST['send_message_id'])){
    echo $_POST['send_message_id'];
    header("location:index.php");
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
  <div class="container friends-outer-container">
  
    

    <h3 class="text-center top-title">Chat Application using PHP Ajax Jquery</h3>

    
    <h4 class="manage-friend"><a href="index.php"  class="messages-btn">Messages <span id="msg-count" class="label label-primary"></span></a></h4>
    
    <p class="text-right">Hi - <strong> <?php echo $_SESSION['username'];  ?> </strong> - <a href="logout.php">Logout</a></p>
    <div class="friends-container">
        
        <div class="container-header">
            <div class="friends active-header header-item">   
                <span class="header-item-inner">All Friends  <span id="friends_count">0</span></span>
                <div class="header-item-overlay"></div>
            </div>
            <div class="friend-reqs  header-item">
                <span class="header-item-inner">Friend Requests <span id="friend_reqs_count">0</span></span>
                <div class="header-item-overlay"></div>
            </div>
            <div class="find-friends  header-item">
                <span class="header-item-inner">Find Friends</span>
                <div class="header-item-overlay"></div>
            </div>
        </div>
        <div class="container-body">
            
            <ul class="friend-list" id="list-container">
                <form method="post" id="search_form">
                    <div class="form-group">
                        <div>
                            <input type="text" id="search-name" class="form-control" placeholder="Type here">
                        </div>
                        <div>
                            <input type="button" value="Search" name="submit" class="form-control btn" id='search_submit'>
                        </div>
                        <div class="dropdown-container">
                            <span> By </span>
                            <select name="search-select" id="search-select" class="form-control">
                                <option value="name" selected>Username</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div id="list-container-inner"></div>
            </ul>
        </div>
    </div>
    
  </div>
</body>

</html>

<script>
$(document).ready(function () {



    fetch_unread_msg_count()
    setInterval(() => {
        fetch_unread_msg_count()
        fetch_user_friends(1) 
        fetch_user_friends(2) 
        fetch_user_friend_reqs(1)
        fetch_user_friend_reqs(2)
    }, 2000);
    


    fetch_user_friends(1)
    fetch_user_friends(2)   // for length
    fetch_user_friend_reqs(2)  // for length



    document.querySelectorAll('.header-item-overlay').forEach(item => {
        item.addEventListener('click', e => {
            if (e.target.parentElement.classList.contains('header-item')){
                if (e.target.parentElement.classList.contains('friends')) {
                    $('.friends').addClass('active-header')
                    $('.friend-reqs').removeClass('active-header')
                    $('.find-friends').removeClass('active-header')

                    $('#list-container').removeClass()
                    $('#list-container').addClass('friend-list')
                    fetch_user_friends(1)
                } else if (e.target.parentElement.classList.contains('friend-reqs')) {
                    $('.friends').removeClass('active-header')
                    $('.friend-reqs').addClass('active-header')
                    $('.find-friends').removeClass('active-header')

                    $('#list-container').removeClass()
                    $('#list-container').addClass('friend-reqs-list')
                    fetch_user_friend_reqs(1)  // for data
                } else {
                    $('.friends').removeClass('active-header')
                    $('.friend-reqs').removeClass('active-header')
                    $('.find-friends').addClass('active-header')

                    $('#list-container').removeClass()
                    $('#list-container').addClass('friend-find-list')
                    fetch_find_friends()  // for data
                }
            }
        })
    })

    document.getElementById('search_submit').addEventListener('click', (e) => {
        e.preventDefault();
        getFormValuesAndRender()
    })

    document.getElementById('list-container-inner').addEventListener('click', (e) => {
        e.preventDefault();
        
        if (e.target.classList.contains('btn')) {
            let target_id = e.target.getAttribute('data-id')
            let target_value = e.target.innerText

            if (target_value == "Send Message") send_message(target_id)
            if (target_value == "Unfriend") unfriend(target_id)
            if (target_value == "Accept") accept_req(target_id)
            if (target_value == "Delete") delete_req(target_id)
            if (target_value == "Send Friend Request") send_friend_request(target_id, e.target)
        }
    })

    function send_message(target_id) {
        $.ajax({
            url: 'send_message.php',
            method: "POST",
            data: {
                send_message_id: target_id
            },
            success: function (data) {
                window.location = 'index.php'
            }
        })
    }

    function unfriend(target_id) {
        $.ajax({
            url: 'unfriend.php',
            method: "POST",
            data: {
                to_user_id: target_id
            },
            success: function (data) {
                fetch_user_friends(1)
                fetch_user_friends(2)
                fetch_user_friend_reqs(1)  
                fetch_user_friend_reqs(2)  // for length
            }
        })
    }

    function accept_req(target_id) {
        $.ajax({
            url: 'accept_req.php',
            method: "POST",
            data: {
                to_user_id: target_id
            },
            success: function (data) {
                fetch_user_friends(2)   // for length
                fetch_user_friend_reqs(1)  
                fetch_user_friend_reqs(2)  // for length
            }
        })
    }

    function delete_req(target_id) {
        unfriend(target_id)
    }

    function send_friend_request(target_id, target_btn) {
        $.ajax({
            url: 'sent_req.php',
            method: "POST",
            data: {
                to_user_id: target_id
            },
            success: function (data) {
                target_btn.innerText = 'Request Sent'
                target_btn.classList.add('sent_req_btn')
            }
        })
    }






    let search_value = '',
        search_key = ''

    function getFormValuesAndRender() {
        search_value = $('#search-name').val()
        search_key = $('#search-select').val()
        if(document.querySelector('.friends').classList.contains('active-header') && search_value != '') fetch_all_search("fetch_friends_search.php", search_value, search_key)
        else if (document.querySelector('.friends').classList.contains('active-header') && search_value == '') fetch_user_friends(1)
        else if (document.querySelector('.friend-reqs').classList.contains('active-header') && search_value != '') fetch_all_search("fetch_friend_reqs_search.php", search_value, search_key)
        else if (document.querySelector('.friend-reqs').classList.contains('active-header') && search_value == '') fetch_user_friend_reqs(1)
        else if (document.querySelector('.find-friends').classList.contains('active-header') && search_value != '') fetch_all_search("fetch_find_friends_search.php", search_value, search_key)
        else if (document.querySelector('.find-friends').classList.contains('active-header') && search_value == '') fetch_find_friends()
            
    }

    
    
    function fetch_all_search(url, search_value, search_key) {
        $.ajax({
            url: url,
            method: "POST",
            data: {
                search_value: search_value,
                search_key: search_key
            },
            success: function (data) {
                $('#list-container-inner').html(data)
            }
        })
    }


    function fetch_user_friend_reqs(x) {
        $.ajax({
            url: "fetch_user_friend_reqs.php",
            method: "POST",
            data: {
                data_mode: x
            },
            success: function (data) {
                if (x == 1) {
                    $('.friend-reqs-list #list-container-inner').html(data)
                } else {
                    $('#friend_reqs_count').html(data)
                }
            }
        })
    }
    




    function fetch_user_friends(x) {
        $.ajax({
            url: "fetch_user_friends.php",
            method: "POST",
            data: {
                data_mode: x
            },
            success: function (data) {
                if (x == 1) {
                    $('.friend-list #list-container-inner').html(data);
                } else {
                    $('#friends_count').html(data)
                }
            }
        })
    }
    


    function fetch_find_friends() {
        $.ajax({
            url: "fetch_find_friends.php",
            method: "POST",
            data: {},
            success: function (data) {
                $('.friend-find-list #list-container-inner').html(data);
            }
        })
    }


    function fetch_unread_msg_count() {
        $.ajax({
            url: "fetch_unread_msg_count.php",
            method: "POST",
            data: {},
            success: function (data) {
                $('#msg-count').html(data)
            }
        })
    }



    

});
</script>