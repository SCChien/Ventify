<?php
include('./core/conn.php');

$email = null;

if(isset($_GET['email'])) {
    $email = str_replace("%40", "@", $_GET['email']);
} else {
    header("Location: error_page.php");
    exit;
}

if(isset($_POST['submit'])){
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        echo "Passwords do not match";
        exit;
    }

    $sql = "UPDATE users SET password = '$new_password' WHERE email = '$email'";

    if ($conn->query($sql) === TRUE) {
        if ($conn->affected_rows > 0) {
            echo "Password updated successfully!";
        } else {
            echo "No rows updated!";
        }
    } else {
        echo "Error updating password: " . $conn->error;
    }
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
    <h2>Reset Your Password</h2>
    <form method="post">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <p>
            <label for="new_password">New Password:</label>
            <input type="text" name="new_password" required>
        </p>
        <p>
            <label for="confirm_password">Confirm Password:</label>
            <input type="text" name="confirm_password" required>
        </p>
        <button type="submit" name="submit">Reset Password</button>
    </form>
</body>
</html>
