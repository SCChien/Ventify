<?php
session_start();

// Include the database connection file
include('./core/conn.php');

// Function to update password
function updatePassword($conn, $userId, $newPassword) {
    $update_query = "UPDATE users SET password = '$newPassword' WHERE id = $userId";
    return $conn->query($update_query);
}

// Function to update email
function updateEmail($conn, $userId, $newEmail) {
    $update_query = "UPDATE users SET email = '$newEmail' WHERE id = $userId";
    return $conn->query($update_query);
}

// Function to update telephone number
function updateTelephone($conn, $userId, $newTelephone) {
    $update_query = "UPDATE users SET telephone = '$newTelephone' WHERE id = $userId";
    return $conn->query($update_query);
}

// Function to delete a user
function deleteUser($conn, $userId) {
    $delete_query = "DELETE FROM users WHERE id = $userId";
    return $conn->query($delete_query);
}

// Check if the form is submitted for updating or deleting users
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_password"])) {
        // Update password
        $userId = $_POST["user_id"];
        $newPassword = $_POST["new_password"];

        if (updatePassword($conn, $userId, $newPassword)) {
            echo "Password updated successfully.";
        } else {
            echo "Error updating password.";
        }
    } elseif (isset($_POST["update_email"])) {
        // Update email
        $userId = $_POST["user_id"];
        $newEmail = $_POST["new_email"];

        if (updateEmail($conn, $userId, $newEmail)) {
            echo "Email updated successfully.";
        } else {
            echo "Error updating email.";
        }
    } elseif (isset($_POST["update_telephone"])) {
        // Update telephone number
        $userId = $_POST["user_id"];
        $newTelephone = $_POST["new_telephone"];

        if (updateTelephone($conn, $userId, $newTelephone)) {
            echo "Telephone number updated successfully.";
        } else {
            echo "Error updating telephone number.";
        }
    } elseif (isset($_POST["delete"])) {
        // Delete user
        $userId = $_POST["user_id"];

        if (deleteUser($conn, $userId)) {
            echo "User deleted successfully.";
        } else {
            echo "Error deleting user.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" href="image/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div id="viewport">
        <!-- 侧边栏 -->
        <div id="sidebar">
            <header>
                <a href="#">我的应用</a>
            </header>
            <ul class="nav">
                <li>
                    <a href="#" data-target="dashboard">
                        仪表盘
                    </a>
                </li>
                <li>
                    <a href="#" data-target="delete">
                        快捷方式
                    </a>
                </li>
                <li>
                    <a href="#" data-target="overview">
                        概览
                    </a>
                </li>
                <li>
                    <a href="#" data-target="events">
                        事件
                    </a>
                </li>
                <li>
                    <a href="#" data-target="about">
                        关于
                    </a>
                </li>
                <li>
                    <a href="#" data-target="services">
                        服务
                    </a>
                </li>
                <li>
                    <a href="#" data-target="contact">
                        联系我们
                    </a>
                </li>
            </ul>
        </div>
        <!-- 内容区域 -->
        <div id="content">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                    <h2>Admin Panel</h2>
                    </div>
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#">
                                <i class="fas fa-user"></i>
                            </a>
                        </li>
                        <li>
                            <a href="#">测试用户</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="container-fluid">
                <div id="dashboard" class="page">
                    <h1>Edit User Information</h1>
                    <!-- Update Password Form -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <label for="user_id_password">User ID:</label>
                        <input type="number" id="user_id_password" name="user_id" required>
                        <br>
                        <label for="new_password">New Password:</label>
                        <input type="password" id="new_password" name="new_password">
                        <br>
                        <input type="submit" name="update_password" value="Update Password">
                    </form>
                    <!-- Update Email Form -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <label for="user_id_email">User ID:</label>
                        <input type="number" id="user_id_email" name="user_id" required>
                        <br>
                        <label for="new_email">New Email:</label>
                        <input type="email" id="new_email" name="new_email">
                        <br>
                        <input type="submit" name="update_email" value="Update Email">
                    </form>
                    <!-- Update Telephone Form -->
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <label for="user_id_telephone">User ID:</label>
                        <input type="number" id="user_id_telephone" name="user_id" required>
                        <br>
                        <label for="new_telephone">New Telephone:</label>
                        <input type="text" id="new_telephone" name="new_telephone">
                        <br>
                        <input type="submit" name="update_telephone" value="Update Telephone">
                    </form>
                </div>
                <div id="delete" class="page">
                    <h1>Delete User</h1>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <label for="delete_user_id">User ID to Delete:</label>
                        <input type="number" id="delete_user_id" name="user_id" required>
                        <br>
                        <input type="submit" name="delete" value="Delete">
                    </form>
                </div>
                <div id="overview" class="page">
                    <h1>All Users</h1>
                    <form action="#" method="post">
                        <button id="showUsersBtn" type="submit">Show Users</button>
                        <div id="userList" style="display: none;"></div>
                    </form>
                </div>
                <div id="events" class="page">
                <h1>Upload Music</h1>
                    <form action="upload_music.php" method="post" enctype="multipart/form-data">
                        <label for="music_name">Music Name:</label>
                        <input type="text" name="music_name" id="music_name" required><br>

                        <label for="artist">Artist:</label>
                        <input type="text" name="artist" id="artist" required><br>

                        <label for="category">Category:</label>
                        <input type="text" name="category" id="category" required><br>

                        <input type="file" name="music_file" accept=".mp3" required><br>

                        <button type="submit" name="submit">Upload</button>
                    </form>
                </div>
                <div id="about" class="page">
                    <h1>关于内容</h1>
                    <p>这是关于的内容。</p>
                </div>
                <div id="services" class="page">
                    <h1>服务内容</h1>
                    <p>这是服务的内容。</p>
                </div>
                <div id="contact" class="page">
                    <h1>联系我们内容</h1>
                    <p>这是联系我们的内容。</p>
                </div>
            </div>
        </div>
    </div>

<script src="./js/script.js"></script>
</body>
</html>