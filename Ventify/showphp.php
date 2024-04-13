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
        // 在这里可以执行你的逻辑，比如设置登录状态、记录登录日志等
        // 更新用户登录后将头像路径设置为空
        $userId = $user['id'];
        $updateSql = "UPDATE users SET avatar_path = NULL WHERE id = $userId";
        $conn->query($updateSql);

        // 获取用户头像路径
        $avatarPath = $user['avatar_path'];

        echo "登录成功！<br>";
        // 显示用户头像
        if (!empty($avatarPath)) {
            echo '<img src="' . $avatarPath . '" alt="User Avatar">';
        } else {
            echo "用户尚未上传头像。";
        }
    } else {
        echo "用户名或密码错误！";
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
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login & Display Avatar</title>
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
    <h3>Go back</h3>
    <a href="index.html">here</a>
</body>
</html>