<?php
session_start();
include('./core/conn.php');

// Check if the user is logged in
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Retrieve user information from the database
    $user_query = "SELECT id, pfp, telephone, email, role FROM users WHERE username = '$username'";
    $user_result = $conn->query($user_query);

    if ($user_result->num_rows == 1) {
        // Fetch the user's information
        $row = $user_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['pfp'];
        $telephone = $row['telephone'];
        $email = $row['email'];
        $role = $row['role'];
        $payment_query = "SELECT * FROM payment WHERE user_id = $user_id ORDER BY payment_date DESC";
        $payment_result = $conn->query($payment_query);

        

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Upload avatar
            if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatarName = $_FILES['avatar']['name'];
                $avatarTmpName = $_FILES['avatar']['tmp_name'];
                $avatarPath = 'uploads/' . $avatarName; // Assuming 'uploads' is the directory to store avatars
                if(move_uploaded_file($avatarTmpName, $avatarPath)) {
                    // Update avatar path in the database
                    $updateAvatarSql = "UPDATE users SET pfp = '$avatarPath' WHERE id = $user_id";
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
                $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePasswordSql = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
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
    <title>User Profile</title>
    <link rel="stylesheet" href="./css/userpfp.css">
</head>
<body>

<div class="back">
    <button onclick="window.location.href='./index.php'">Back</button>
</div>
<div class="container">
    <div class="banner">
        <div class="plan-box">
            <div class="plan-title">Your plan</div>
            <div class="plan-type"><?php echo $role; ?></div>
            <?php if ($role === ' NORMAL USER'): ?>
                <div class="premium-button">
                    <button onclick="window.location.href='premium.php'">Join Premium</button>
                </div>
            <?php endif; ?>
        </div>
        <div class="button">
                <button id="payment_history" onclick="showPop_up()">View Payment History</button>
            </div>

    </div>
    <div class="user-info">
        <div class="userpfp">
            <img src="<?php echo $avatarPath; ?>" alt="Profile Picture">
        </div>
        <div class="username">
            <?php echo $username; ?>
        </div>
        <div class="button">
            <button id="edit" onclick="showPopup()">Edit User Profile</button>
        </div>
        
    </div>
    <div class="info">
        <div>Name: <?php echo $username; ?></div>
        <div>ID: <?php echo $user_id; ?></div>
        <div>Email: <?php echo $email; ?></div>
        <div>Phone: <?php echo $telephone; ?></div>
    </div>
</div>

<!-- Edit Profile Popup -->
<div id="editProfilePopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h2>Edit User Profile</h2>
        <div id="editForm" class="edit">
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

<div id="Payment_History" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePop_up()">&times;</span>
        <h2>Payment History</h2>
        <?php
        if ($payment_result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Payment ID</th><th>Amount</th><th>Payment Date</th></tr>";
            while ($payment_row = $payment_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $payment_row['payment_id'] . "</td>";
                echo "<td>" . $payment_row['amount'] . "</td>";
                echo "<td>" . $payment_row['payment_date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No payment history found.</p>";
        }
        ?>
        </div>
</div>

<script src="js/edit.js"></script>
</body>
</html>