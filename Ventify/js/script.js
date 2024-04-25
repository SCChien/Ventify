$(document).ready(function () {
    // Give "Show Users" button an ID for easier selection
    $("#showUsersBtn").click(function (e) {
        e.preventDefault();
        console.log("Show Users button clicked");
        $.ajax({
            url: "fetch_users.php", // Correct path to fetch_users.php
            type: "POST",
            success: function (response) {
                console.log(response); 
                $('#userList').html(response);
                $('#userList').slideDown(); // Show the mini window
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
    

    // Close the mini window when clicking outside of it
    $(document).click(function (event) {
        if (!$(event.target).closest('#userList, #showUsersBtn').length) {
            $('#userList').slideUp(); // Hide the mini window
        }
    });

    // Give left sidebar menu items a class for easier selection
    $(".nav a").click(function () {
        var target = $(this).data('target');
        $('.page').hide();
        $('#' + target).show();
    });

    // Show default dashboard page
    $('#dashboard').show();
});
