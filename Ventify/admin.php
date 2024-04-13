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
    <link rel="stylesheet" href="css\style.css">

    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <!--<img src="logo.png" alt="">-->
                </span>

                <div class="text logo-text">
                    <span class="name">Codinglab</span>
                    <span class="profession">Web developer</span>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">

                <li class="search-box">
                    <i class='bx bx-search icon'></i>
                    <input type="text" placeholder="Search...">
                </li>


                <ul class="menu-links">
                    <li class="nav-link">
                        <a href="showphp.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">showphp</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="userpfp.php">
                            <i class='bx bx-bar-chart-alt-2 icon' ></i>
                            <span class="text nav-text">userpfp</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-bell icon'></i>
                            <span class="text nav-text">Notifications</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-pie-chart-alt icon' ></i>
                            <span class="text nav-text">Analytics</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-heart icon' ></i>
                            <span class="text nav-text">Likes</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-wallet icon' ></i>
                            <span class="text nav-text">Wallets</span>
                        </a>
                    </li>

                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="#">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">Logout</span>
                    </a>
                </li>

                <li class="mode">
                    <div class="sun-moon">
                        <i class='bx bx-moon icon moon'></i>
                        <i class='bx bx-sun icon sun'></i>
                    </div>
                    <span class="mode-text text">Dark mode</span>

                    <div class="toggle-switch">
                        <span class="switch"></span>
                    </div>
                </li>
                
            </div>
        </div>

</nav>
    
<section class="home">
        <div class="text">
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

        <h3>All Users</h3>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="show_users" value="true">
        <button type="submit">Show Users</button>
    </form>

    <!-- Display users if the button is clicked -->
    <?php
    // Include the database connection file
    include('conn.php');

    // Check if the button is clicked
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["show_users"])) {
        // Fetch all users from the database
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);

        // Output users as a table
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Telephone</th>
                    <th>Email</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['username'] . "</td>";
            echo "<td>" . $row['password'] . "</td>";
            echo "<td>" . $row['telephone'] . "</td>";
            echo "<td>" . $row['email'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";

        // Close the connection
        $conn->close();
    }
    ?>
        </div>
</section>
<script src="./js/admin_test.js"></script>
</body>
</html>