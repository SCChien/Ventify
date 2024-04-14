<!-- 这是用户上传头像和显示头像 -->

<?php
// 包含数据库连接文件
include('conn.php');

// 处理用户登录逻辑
function handleLogin($username, $password, $conn) {
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        // 用户登录成功
        $user = $result->fetch_assoc();
        // 保存用户ID到SESSION中
        session_start();
        $_SESSION['user_id'] = $user['id']; // 使用'id'字段
        $_SESSION['avatar_path'] = $user['avatar_path']; // 保存头像路径到Session
        echo "登录成功！";
    } else {
        echo "用户名或密码错误！";
    }
}

// 处理用户上传头像逻辑
function uploadAvatar($avatarPath, $conn) {
    // 从SESSION中获取当前用户ID
    session_start();
    $userId = $_SESSION['user_id'];

    // 更新用户的头像路径
    $updateSql = "UPDATE users SET avatar_path = '$avatarPath' WHERE id = $userId"; // 使用'id'字段
    if ($conn->query($updateSql) === TRUE) {
        $_SESSION['avatar_path'] = $avatarPath; // 更新Session中的头像路径
        echo "头像上传成功！";
    } else {
        echo "Error: " . $updateSql . "<br>" . $conn->error;
    }
}

// 处理表单提交
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 处理用户登录
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        handleLogin($username, $password, $conn);
    }
    // 处理上传头像
    elseif (isset($_POST['upload_avatar'])) {
        // 获取上传的文件
        $avatarFileName = basename($_FILES["avatar"]["name"]);
        $uploadPath = 'uploads/' . $avatarFileName;
        if ($_FILES["avatar"]["error"] !== UPLOAD_ERR_OK) {
            echo "文件上传失败，错误代码：" . $_FILES["avatar"]["error"];
        } elseif (move_uploaded_file($_FILES["avatar"]["tmp_name"], $uploadPath)) {
            echo "头像上传成功！";
            uploadAvatar($uploadPath, $conn);
        } else {
            echo "抱歉，上传失败。";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login & Upload Avatar</title>
</head>
<body>
    <h2>User Login</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" name="login" value="Login">
    </form>

    <!-- 显示头像 -->
    <?php
    session_start();
    if (isset($_SESSION['avatar_path'])) {
        $avatarPath = $_SESSION['avatar_path'];
        echo "<img src='$avatarPath' alt='Avatar'>";
    }
    ?>

    <h2>Upload Avatar</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
        <input type="file" name="avatar" id="avatar" required><br><br>
        <input type="submit" name="upload_avatar" value="Upload Avatar">
    </form>

</body>
</html>
