<?php
session_start();

// Include the database connection file
include('conn.php');

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h2>Admin Panel</h2>

    <h3>Edit User Information</h3>

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

    <h3>Delete User</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="delete_user_id">User ID to Delete:</label>
        <input type="number" id="delete_user_id" name="user_id" required>
        <br>
        <input type="submit" name="delete" value="Delete">
    </form>
</body>
</html>
