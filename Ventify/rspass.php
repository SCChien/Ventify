<?php
include('./core/conn.php');

$email = null;

if (isset($_GET['email'])) {
    $email = str_replace("%40", "@", $_GET['email']);
} else {
    header("Location: error_page.php");
    exit;
}

$message = "";

if (isset($_POST['submit'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = "Passwords do not match";
    } else {
        // 对密码进行哈希处理
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = '$hashed_password' WHERE email = '$email'";

        if ($conn->query($sql) === TRUE) {
            if ($conn->affected_rows > 0) {
                $message = "Password updated successfully!";
            } else {
                $message = "No rows updated!";
            }
        } else {
            $message = "Error updating password: " . $conn->error;
        }
    }

    echo "<script>alert('$message');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/rspass.css">
    <title>Reset Password</title>
</head>

<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        <form method="post">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <p>
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" required>
            </p>
            <p>
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" required>
            </p>
            <button type="submit" name="submit">Reset Password</button>
        </form>
        <button class="back-to-login" onclick="window.location.href='login.php'">Back to Login</button>
    </div>
</body>

</html>