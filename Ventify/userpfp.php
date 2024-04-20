<?php
session_start();
include('./core/conn.php');

// Check if the user is logged in
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Retrieve user information from the database
    $user_query = "SELECT id, pfp, telephone, email FROM users WHERE username = '$username'";
    $user_result = $conn->query($user_query);

    if ($user_result->num_rows == 1) {
        // Fetch the user's information
        $row = $user_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['pfp'];
        $telephone = $row['telephone'];
        $email = $row['email'];
    } else {
        echo "用户不存在！";
    }

    // Decode the JSON playlist data
    $playlistFile = './sql/playlist.json';
    $playlistData = json_decode(file_get_contents($playlistFile), true);

    // Find the playlist of the current user
    $userPlaylist = null;
    foreach ($playlistData as $playlist) {
        if ($playlist['username'] === $username) {
            $userPlaylist = $playlist['songs'];
            break;
        }
    }
} else {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="./css/userpfp.css">
</head>
<body>

    <div class="back">
        <button onclick="window.location.href='./index.php'">Back</button>
    </div>
    <div class="container">
        <div class="profile-box">
            <div class="banner"></div>
            <div class="user-info">
                <div class="userpfp">
                    <img src="<?php echo $avatarPath; ?>" alt="Profile Picture">
                </div>
                <div class="username">
                    <p><?php echo $username; ?></p>
                </div>
                <div class="button">
                    <button id="edit" onclick="showPopup()">Edit User Profile</button>
                </div>

            </div>

            <!-- Edit Profile Popup -->
    <div id="editProfilePopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">&times;</span>
            <h2>Edit User Profile</h2>
            <div id="editForm" class="edit" style="display: none">
                <h3>修改密码</h3>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="password" name="new_password" placeholder="新密码" required><br><br>
                    <input type="submit" name="update_password" value="更新密码">
                </form>

                <h3>修改电话号码</h3>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="text" name="new_telephone" placeholder="新电话号码" value="<?php echo $telephone; ?>" required><br><br>
                    <input type="submit" name="update_telephone" value="更新电话号码">
                </form>

                <h3>修改邮箱</h3>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="email" name="new_email" placeholder="新邮箱" value="<?php echo $email; ?>" required><br><br>
                    <input type="submit" name="update_email" value="更新邮箱">
                </form>

                <h3>上传头像</h3>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                    <input type="file" name="avatar" accept="image/*" required><br><br>
                    <input type="submit" name="upload_avatar" value="上传头像">
                </form>
            </div>
        </div>
    </div>
            <div class="info">
                <div class="username1">Name:
                    <div class="username2"><?php echo $username; ?></div>
                </div>
                <div class="userID">ID:
                    <div class="userID2"><?php echo $user_id; ?></div>
                </div>
                <div class="userEmail">Email: 
                    <div class="userEmail2"><?php echo $email; ?></div>
                </div>
                <div class="userPhone">Phone:
                    <div class="userPhone2"><?php echo $telephone; ?></div>
                </div>
            </div>

            <!-- Display user's playlist -->
            

        </div>
    </div>

    <script src="js/edit.js"></script>
</body>

</html>
