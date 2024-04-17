<?php
session_start();
include('conn.php');

// Check if the user is logged in
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Retrieve user information from the database
    $user_query = "SELECT id, avatar_path, telephone, email FROM users WHERE username = '$username'";
    $user_result = $conn->query($user_query);

    if ($user_result->num_rows == 1) {
        // Fetch the user's information
        $row = $user_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['avatar_path'];
        $telephone = $row['telephone'];
        $email = $row['email'];

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Upload avatar
            if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatarName = $_FILES['avatar']['name'];
                $avatarTmpName = $_FILES['avatar']['tmp_name'];
                $avatarPath = 'uploads/' . $avatarName; // Assuming 'uploads' is the directory to store avatars
                if(move_uploaded_file($avatarTmpName, $avatarPath)) {
                    // Update avatar path in the database
                    $updateAvatarSql = "UPDATE users SET avatar_path = '$avatarPath' WHERE id = $user_id";
                    if ($conn->query($updateAvatarSql) === TRUE) {
                        echo "头像上传成功！";
                    } else {
                        echo "Error updating avatar: " . $conn->error;
                    }
                } else {
                    echo "头像上传失败！";
                }
            }
            // Update password
            if (isset($_POST['update_password'])) {
                $newPassword = $_POST['new_password'];
                $updatePasswordSql = "UPDATE users SET password = '$newPassword' WHERE id = $user_id";
                if ($conn->query($updatePasswordSql) === TRUE) {
                    echo "密码更新成功！";
                } else {
                    echo "Error updating password: " . $conn->error;
                }
            }
            // Update telephone number
            if (isset($_POST['update_telephone'])) {
                $newTelephone = $_POST['new_telephone'];
                $updateTelephoneSql = "UPDATE users SET telephone = '$newTelephone' WHERE id = $user_id";
                if ($conn->query($updateTelephoneSql) === TRUE) {
                    echo "电话号码更新成功！";
                } else {
                    echo "Error updating telephone number: " . $conn->error;
                }
            }
            // Update email
            if (isset($_POST['update_email'])) {
                $newEmail = $_POST['new_email'];
                $updateEmailSql = "UPDATE users SET email = '$newEmail' WHERE id = $user_id";
                if ($conn->query($updateEmailSql) === TRUE) {
                    echo "邮箱更新成功！";
                } else {
                    echo "Error updating email: " . $conn->error;
                }
            }
        }
    } else {
        echo "用户不存在！";
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
    <title>Profile</title>
</head>
<body>
    <h2>Profile</h2>
    <p>用户名：<?php echo $username; ?></p>
    <p>电话号码：<?php echo $telephone; ?></p>
    <p>邮箱：<?php echo $email; ?></p>
    <p>头像：<img src="<?php echo $avatarPath; ?>" alt="Avatar"></p>

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

    <h3>Go Back</h3>
    <a href="index.php">NI Ma SI Le</a>
</body>
</html>
