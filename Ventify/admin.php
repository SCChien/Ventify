<?php
// Session start
session_start();
// Include the database connection file
include('./core/conn.php');

$success_message = "";
$error_message = "";

// Check if the form is submitted for adding admin by superadmin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_admin"])) {
    // Check if current user is superadmin
    if ($_SESSION["username"] === "venti") {
        $new_admin_username = $_POST["new_admin_username"];
        $new_admin_password = $_POST["new_admin_password"];
        $new_admin_email = $_POST["new_admin_email"];

        // Check if username is already taken
        $check_query = "SELECT * FROM admin WHERE username = '$new_admin_username'";
        $check_result = $conn->query($check_query);

        if ($check_result->num_rows == 0) {
            // Insert new admin into the database
            $insert_query = "INSERT INTO admin (username, password, email) VALUES ('$new_admin_username', '$new_admin_password', '$new_admin_email')";
            if ($conn->query($insert_query) === TRUE) {
                // Set success message
                $success_message = "New admin added successfully!";
            } else {
                // Display error message for database error
                $error_message = "Error: " . $conn->error;
            }
        } else {
            // Display error message for duplicate username
            $error_message = "Username already taken. Please choose another username.";
        }
    } else {
        // Display error message for non-superadmin users trying to add admin
        $error_message = "Only Venti can add new admins.";
    }
}

// Fetch all admins from the database
$admins_query = "SELECT * FROM admin";
$admins_result = $conn->query($admins_query);

// Check if the form is submitted for displaying payments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_display"])) {
    $display_option = $_POST["display_option"];
    if ($display_option == "user_payments") {
        // Display user payments
        $payments_query = "SELECT users.username, payment.amount, payment.payment_date FROM users INNER JOIN payment ON users.id = payment.user_id";
        $payments_result = $conn->query($payments_query);
    } elseif ($display_option == "total_payment") {
        // Calculate total payment amount
        $total_payment_query = "SELECT SUM(amount) AS total_amount FROM payment";
        $total_payment_result = $conn->query($total_payment_query);
        $total_payment_row = $total_payment_result->fetch_assoc();
        $total_payment_amount = $total_payment_row['total_amount'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["promote_admin"])) {
    // Check if current user is superadmin
    if ($_SESSION["username"] === "venti") {
        $user_to_promote = $_POST["user_to_promote"];

        // Check if the user exists and is not already an admin
        $check_user_query = "SELECT * FROM users WHERE username = '$user_to_promote' AND role != 'Admin'";
        $check_user_result = $conn->query($check_user_query);

        if ($check_user_result->num_rows > 0) {
            // Promote the user to admin
            $promote_query = "UPDATE users SET role='Admin' WHERE username='$user_to_promote'";
            if ($conn->query($promote_query) === TRUE) {
                // Set success message
                $success_message = "User '$user_to_promote' promoted to admin successfully!";
            } else {
                // Display error message for database error
                $error_message = "Error: " . $conn->error;
            }
        } else {
            // Display error message if user doesn't exist or is already an admin
            $error_message = "User '$user_to_promote' either doesn't exist or is already an admin.";
        }
    } else {
        // Display error message for non-superadmin users trying to promote user to admin
        $error_message = "Only Venti can promote users to admin.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_admin"])) {
    // Check if current user is superadmin
    if ($_SESSION["username"] === "venti") {
        $new_admin_username = $_POST["new_admin_username"];
        $new_admin_password = $_POST["new_admin_password"];
        $new_admin_email = $_POST["new_admin_email"];

        // Check if username is already taken
        $check_query = "SELECT * FROM admin WHERE username = '$new_admin_username'";
        $check_result = $conn->query($check_query);

        if ($check_result->num_rows == 0) {
            // Insert new admin into the database
            $insert_query = "INSERT INTO admin (username, password, email) VALUES ('$new_admin_username', '$new_admin_password', '$new_admin_email')";
            if ($conn->query($insert_query) === TRUE) {
                // Set success message
                $success_message = "New admin added successfully!";
            } else {
                // Display error message for database error
                $error_message = "Error: " . $conn->error;
            }
        } else {
            // Display error message for duplicate username
            $error_message = "Username already taken. Please choose another username.";
        }
    } else {
        // Display error message for non-superadmin users trying to add admin
        $error_message = "Only Venti can add new admins.";
    }
}

// Fetch all users from the database
$users_query = "SELECT * FROM users";
$users_result = $conn->query($users_query);
// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION["username"]; ?>!</h1>

    <!-- Add New Admin Form -->
    <h2>Add New Admin</h2>
    <?php if ($_SESSION["username"] === "venti") : ?>
        <form method="POST" action="">
            <label for="new_admin_username">Username:</label>
            <input type="text" name="new_admin_username" required><br>
            <label for="new_admin_password">Password:</label>
            <input type="password" name="new_admin_password" required><br>
            <label for="new_admin_email">Email:</label>
            <input type="email" name="new_admin_email" required><br>
            <input type="submit" name="add_admin" value="Add Admin">
        </form>
    <?php else : ?>
        <p>Only superadmin can add new admins.</p>
    <?php endif; ?>

    <!-- Display Success or Error Messages -->
    <?php
    if (!empty($success_message)) {
        echo "<p style='color: green;'>$success_message</p>";
    }
    if (!empty($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <!-- Display Options Form -->
    <h2>Display Options</h2>
    <form method="POST" action="">
        <label for="display_option">Select Display Option:</label>
        <select name="display_option" id="display_option">
            <option value="user_payments">User Payments</option>
            <option value="total_payment">Total Payment</option>
        </select>
        <button type="submit" name="submit_display">Display</button>
    </form>

    <!-- Display User Payments -->
    <?php if (isset($payments_result)) : ?>
        <h2>User Payments</h2>
        <ul>
            <?php
            if ($payments_result && $payments_result->num_rows > 0) {
                while ($row = $payments_result->fetch_assoc()) {
                    echo "<li>{$row['username']} - {$row['amount']} - {$row['payment_date']}</li>";
                }
            } else {
                echo "<li>No payments found.</li>";
            }
            ?>
        </ul>
    <?php endif; ?>

    <!-- Display Total Payment Amount -->
    <?php if (isset($total_payment_amount)) : ?>
        <h2>Total Payment Amount</h2>
        <p><?php echo "Total amount paid: " . number_format($total_payment_amount, 2); ?></p>
    <?php endif; ?>

    <!-- Display Admin List -->
    <h2>Admin List</h2>
    <ul>
        <?php
        if ($admins_result && $admins_result->num_rows > 0) {
            while ($row = $admins_result->fetch_assoc()) {
                echo "<li>{$row['username']} - {$row['email']}</li>";
            }
        } else {
            echo "<li>No admins found.</li>";
        }
        ?>
    </ul>

    <!-- Promote User to Admin Form -->
    <h2>Promote User to Admin</h2>
    <?php if ($_SESSION["username"] === "venti") : ?>
        <form method="POST" action="">
            <label for="user_to_promote">Username:</label>
            <input type="text" name="user_to_promote" required><br>
            <input type="submit" name="promote_admin" value="Promote to Admin">
        </form>
    <?php else : ?>
        <p>Only superadmin can promote users to admin.</p>
    <?php endif; ?>

    <!-- Button to Show User List -->
    <h1>User List</h1>

<!-- Display Users -->
<?php if ($users_result && $users_result->num_rows > 0) : ?>
    <table>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Telephone</th>
            <th>Role</th>
        </tr>
        <?php while ($row = $users_result->fetch_assoc()) : ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['telephone']; ?></td>
                <td><?php echo $row['role']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else : ?>
    <p>No users found.</p>
<?php endif; ?>

    <!-- Logout Link -->
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
