<?php
session_start();
include('./core/conn.php');

// Check if the user is logged in
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Retrieve user information from the database
    $user_query = "SELECT id, pfp, telephone, email, password, role FROM users WHERE username = '$username'";
    $user_result = $conn->query($user_query);

    if ($user_result->num_rows == 1) {
        // Fetch the user's information
        $row = $user_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['pfp'];
        $telephone = $row['telephone'];
        $email = $row['email'];
        $currentPassword = $row['password'];
        $role = $row['role'];
        $payment_query = "SELECT * FROM payment WHERE user_id = $user_id ORDER BY payment_date DESC";
        $payment_result = $conn->query($payment_query);
        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $message = "";
            // Upload avatar
            if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                $avatarName = $_FILES['avatar']['name'];
                $avatarTmpName = $_FILES['avatar']['tmp_name'];
                $avatarPath = 'uploads/' . $avatarName; // Assuming 'uploads' is the directory to store avatars
                if(move_uploaded_file($avatarTmpName, $avatarPath)) {
                    // Update avatar path in the database
                    $updateAvatarSql = "UPDATE users SET pfp = '$avatarPath' WHERE id = $user_id";
                    if ($conn->query($updateAvatarSql) === TRUE) {
                        $message .= "Avatar updated successful\\n";
                    } else {
                        $message .= "Error updating avatar: " . $conn->error . "\\n";
                    }
                } else {
                    $message .= "Error updating avatar!\\n";
                }
            }
            // Update password
            if (isset($_POST['update_password'])) {
                $oldPassword = $_POST['old_password'];
                $newPassword = $_POST['new_password'];
                if (password_verify($oldPassword, $currentPassword)) {
                    if (strlen($newPassword) >= 6) {
                        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
                        $updatePasswordSql = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
                        if ($conn->query($updatePasswordSql) === TRUE) {
                            $message .= "Password updated successful\\n";
                        } else {
                            $message .= "Error updating password: " . $conn->error . "\\n";
                        }
                    } else {
                        $message .= "New password length cannot less than 6 digit\\n";
                    }
                } else {
                    $message .= "Current password incorrect!\\n";
                }
            }
            // Update telephone number
            if (isset($_POST['update_telephone'])) {
                $newTelephone = $_POST['new_telephone'];
                $updateTelephoneSql = "UPDATE users SET telephone = '$newTelephone' WHERE id = $user_id";
                if ($conn->query($updateTelephoneSql) === TRUE) {
                    $message .= "Hp Number updated successful!\\n";
                } else {
                    $message .= "Error updating telephone number: " . $conn->error . "\\n";
                }
            }
            // Update email
            if (isset($_POST['update_email'])) {
                $newEmail = $_POST['new_email'];
                $updateEmailSql = "UPDATE users SET email = '$newEmail' WHERE id = $user_id";
                if ($conn->query($updateEmailSql) === TRUE) {
                    $message .= "Email updated successful\\n";
                } else {
                    $message .= "Error updating email: " . $conn->error . "\\n";
                }
            }
            echo "<script>alert('$message');</script>";
        }
    } else {
        echo "<script>alert('Invalid User!');</script>";
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
<header>
  <a href="index.php"><img src="./image/icon_white.png"><span>entify</span></a>
</header>

<div class="back">
    <h3>Welcome</h3>
</div>
<div class="container">
<div class="banner">
    <div class="plan-box">
        <div class="plan-title">Your plan</div>
        <div class="plan-type"><?php echo $role; ?></div>
        <?php if ($role === 'NORMAL USER'): ?>
            <div class="premium-button">
                <button onclick="window.location.href='premium.php'">Join Premium</button>
            </div>
        <?php else: ?>
            <div class="button">
                <button id="payment_history" onclick="showPop_up()">View Payment History</button>
            </div>
        <?php endif; ?>
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
            <h3>Edit Password</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="password" name="old_password" placeholder="Current Password" required><br><br>
                <input type="password" name="new_password" placeholder="New Password" required><br><br>
                <input type="submit" name="update_password" value="Update Password">
            </form>

            <h3>Edit Hp Number</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="text" name="new_telephone" placeholder="New Hp Number." value="<?php echo $telephone; ?>" required><br><br>
                <input type="submit" name="update_telephone" value="Update Hp Number">
            </form>

            <h3>Edit Email</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="email" name="new_email" placeholder="New Email" value="<?php echo $email; ?>" required><br><br>
                <input type="submit" name="update_email" value="Update Email">
            </form>

            <h3>Update Avatar</h3>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                <input type="file" name="avatar" accept="image/*" required><br><br>
                <input type="submit" name="upload_avatar" value="Update Avatar">
            </form>
        </div>
    </div>
</div>

<!-- Payment History Popup -->
<div id="Payment_History" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePop_up()">&times;</span>
        <h2>Payment History</h2>
        <?php
        $username = $_SESSION['username'];

        // 获取用户ID
        $user_id_query = "SELECT id FROM users WHERE username = ?";
        $stmt = $conn->prepare($user_id_query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user_id_result = $stmt->get_result();
        if ($user_id_result->num_rows == 1) {
            $user_row = $user_id_result->fetch_assoc();
            $user_id = $user_row['id'];
        } else {
            echo "<p>Unable to retrieve user ID.</p>";
            exit();
        }
        $stmt->close();

        // 自定义计划名称映射
        $custom_plan_names = array(
            '1' => 'VIP Individual',
            '2' => 'VIP Student',
            '3' => 'VIP Family',
            // 添加更多自定义计划
        );

        $custom_plan_duration= array(
            '1' => '30',
            '2' => '30',
            '3' => '30',
            // 添加更多自定义计划
        );

        

        // 查询支付记录并联接计划名称和持续时间
        $payment_query = "SELECT payment.payment_id, payment.amount, payment.payment_date, payment.plan_id, plans.title,plans.duration
                          FROM payment 
                          LEFT JOIN plans ON payment.plan_id = plans.plan_id 
                          WHERE payment.user_id = ?";
        $stmt = $conn->prepare($payment_query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $payment_result = $stmt->get_result();

        if ($payment_result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Payment ID</th><th>Amount</th><th>Payment Date</th><th>Plan Name</th><th>Start Date</th><th>Duration (Days)</th></tr>";
            while ($payment_row = $payment_result->fetch_assoc()) {
                $plan_id = $payment_row['plan_id'];
                $plan_name = $payment_row['title'];
                $plan_duration = $payment_row['duration'];

                // 如果计划名称为空，尝试使用自定义计划名称
                if (empty($plan_name) && isset($custom_plan_names[$plan_id])) {
                    $plan_name = $custom_plan_names[$plan_id];
                } elseif (empty($plan_name)) {
                    $plan_name = 'Unknown Plan';
                }

                if (empty($plan_duration) && isset($custom_plan_duration[$plan_id])) {
                    $plan_duration = $custom_plan_duration[$plan_id];
                } elseif (empty($plan_name)) {
                    $plan_duration = '0';
                }

                echo "<tr>";
                echo "<td>" . htmlspecialchars($payment_row['payment_id']) . "</td>";
                echo "<td>" . htmlspecialchars($payment_row['amount']) . "</td>";
                echo "<td>" . htmlspecialchars($payment_row['payment_date']) . "</td>";
                echo "<td>" . htmlspecialchars($plan_name) . "</td>";
                echo "<td>" . htmlspecialchars($payment_row['payment_date']) . "</td>";
                echo "<td>" . htmlspecialchars($plan_duration) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No payment history found.</p>";
        }
        $stmt->close();
        ?>
    </div>
</div>

<script src="./js/edit.js"></script>
</body>
</html>