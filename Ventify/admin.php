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
        $hashed_password = password_hash($new_admin_password, PASSWORD_DEFAULT);

        // Check if username is already taken
        $check_query = "SELECT * FROM admin WHERE username = '$new_admin_username'";
        $check_result = $conn->query($check_query);

        if ($check_result->num_rows == 0) {
            // Insert new admin into the database
            $insert_query = "INSERT INTO admin (username, password, email) VALUES ('$new_admin_username', '$hashed_password', '$new_admin_email')";
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_plan"])) {
    $plan_title = $_POST["plan_title"];
    $plan_description = $_POST["plan_description"];
    $plan_duration = $_POST["plan_duration"];
    $plan_price = $_POST["plan_price"];

    $insert_plan_query = "INSERT INTO plans (title, description, duration, price) VALUES ('$plan_title', '$plan_description', '$plan_duration', '$plan_price')";

    if ($conn->query($insert_plan_query) === TRUE) {
        // 使用重定向来防止重复提交
        header("Location: admin.php?status=success");
        exit();
    } else {
        $error_message = "Error: " . $conn->error;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edit_plan"])) {
    $plan_id = $_POST["plan_id"];
    $plan_title = $_POST["plan_title"];
    $plan_description = $_POST["plan_description"];
    $plan_duration = $_POST["plan_duration"];
    $plan_price = $_POST["plan_price"];

    $update_plan_query = "UPDATE plans SET title = '$plan_title', description = '$plan_description', duration = '$plan_duration', price = '$plan_price' WHERE plan_id = '$plan_id'";

    if ($conn->query($update_plan_query) === TRUE) {
        // 使用重定向来防止重复提交
        header("Location: admin.php?status=edit_success");
        exit();
    } else {
        $error_message = "Error: " . $conn->error;
    }
}


// 新增删除计划处理逻辑
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_plan"])) {
    $plan_id = $_POST["plan_id"];
    $delete_plan_query = "DELETE FROM plans WHERE plan_id = '$plan_id'";

    try {
        if ($conn->query($delete_plan_query) === TRUE) {
            // 使用重定向来防止重复提交
            header("Location: admin.php?status=delete_success");
            exit();
        } else {
            $error_message = "Error: " . $conn->error;
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) { // Foreign key constraint violation
            $error_message = "This plan cannot be deleted as it is associated with user payments.";
        } else {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Fetch all plans from the database
$plans_query = "SELECT * FROM plans";
$plans_result = $conn->query($plans_query);

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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="icon" href="image/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div id="viewport">
        <!-- 侧边栏 -->
        <div id="sidebar">
            <header>
                <a href="#">Ventify</a>
            </header>
            <ul class="nav">
                <li>
                    <a href="#" data-target="dashboard">
                        Payment History
                    </a>
                </li>
                <li>
                    <a href="#" data-target="delete">
                        Admin Manage
                    </a>
                </li>
                
                <li>
                    <a href="#" data-target="about">
                        User List
                    </a>
                </li>
                <li>
                    <a href="#" data-target="services">
                        Plan Manage
                    </a>
                </li>
                
                <li>
                    <a href="login.php">
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        <!-- 内容区域 -->
        <div id="content">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <h1>Admin Panel</h1>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div id="dashboard" class="page">
            <div class="display_option">
                <h1>Display Options</h1>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="display_option">Select Display Option:</label>
                        <select class="form-control" name="display_option" id="display_option">
                            <option value="user_payments">User Payments</option>
                            <option value="total_payment">Total Payment</option>
                        </select>
                    </div>
                    <button class="btn btn-primary btn-submit" type="submit" name="submit_display">Display</button>
                </form>
            </div>
            <!-- Display User Payments -->
            <div class="Payment_list">
                <?php if (isset($payments_result)) : ?>
                    <h2>User Payments</h2>
                    <div class="table-container">
                        <table class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Amount</th>
                                    <th>Payment Date</th>
                                    <th>Plan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($payments_result && $payments_result->num_rows > 0) {
                                    while ($row = $payments_result->fetch_assoc()) {
                                        $plan_id = $row['plan_id'];
                                        $plan_name = $row['title'];
                                        $custom_plan_names = array(
                                            '1' => 'VIP Individual',
                                            '2' => 'VIP Student',
                                            '3' => 'VIP Family',
                                            // 添加更多自定义计划
                                        );        
                                        // 如果计划名称为空，尝试使用自定义计划名称
                                        if (empty($plan_name) && isset($custom_plan_names[$plan_id])) {
                                            $plan_name = $custom_plan_names[$plan_id];
                                        } elseif (empty($plan_name)) {
                                            $plan_name = 'Unknown Plan';
                                        }
                                        echo "<tr>
                                                <td>{$row['username']}</td>
                                                <td>{$row['amount']}</td>
                                                <td>{$row['payment_date']}</td>
                                                <td>{$plan_name}</td>
                                              </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3'>No payments found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                            
                <!-- Display Total Payment Amount -->
                <?php if (isset($total_payment_amount)) : ?>
                    <h2>Total Payment Amount</h2>
                    <p class="lead text-center"><?php echo "Total amount paid: $" . number_format($total_payment_amount, 2); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div id="delete" class="page">
            <h1>Admin List</h1>
            <div class="admin_list">
                <table class="admin_list">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($admins_result && $admins_result->num_rows > 0) {
                            while ($row = $admins_result->fetch_assoc()) {
                                if($row['status'] == 1){
                                    $status = 'Active';
                                }else{
                                    $status = 'Unactive';
                                }
                                echo "<tr>
                                        <td>{$row['username']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$status}</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='2'>No admins found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- Add New Admin -->
            <h2>Add New Admin</h2>
            <?php if ($_SESSION["username"] === "venti") : ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="new_admin_username">Username:</label>
                        <input type="text" class="form-control" name="new_admin_username" required>
                    </div>
                    <div class="form-group">
                        <label for="new_admin_password">Password:</label>
                        <input type="password" class="form-control" name="new_admin_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_admin_email">Email:</label>
                        <input type="email" class="form-control" name="new_admin_email" required>
                    </div>
                    <button class="btn btn-primary btn-submit" type="submit" name="add_admin">Add Admin</button>
                </form>
            <?php else : ?>
                <p>Only superadmin can add new admins.</p>
            <?php endif; ?>

            <!-- Disable Admin Account Form -->
            <h1>Disable Admin Account</h1>
            <?php if ($_SESSION["username"] === "venti") : ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="admin_username">Admin Username:</label>
                        <select class="form-control" name="admin_username" required>
                            <?php
                            mysqli_data_seek($admins_result, 0);
                            // Populate the select element with the admin usernames
                            if ($admins_result && $admins_result->num_rows > 0) {
                                while ($row = $admins_result->fetch_assoc()) {
                                    echo "<option value='{$row['username']}'>{$row['username']}</option>";
                                }
                                // Reset the pointer of the result set to the beginning
                                mysqli_data_seek($admins_result, 0);
                            } else {
                                echo "<option value=''>No admins found.</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admin_status">Status:</label>
                        <select class="form-control" name="admin_status" required>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                        </select>
                    </div>
                    <button class="btn btn-primary btn-submit" type="submit" name="disable_admin">Update Status</button>
                </form>
            <?php else : ?>
                <p>Only superadmin can disable admin accounts.</p>
            <?php endif; ?>
        </div>
    
     <div id="about" class="page">
         <h1>User List</h1>
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
                            <td><?php echo isset($row['id']) ? $row['id'] : ''; ?></td>
                            <td><?php echo isset($row['username']) ? $row['username'] : ''; ?></td>
                            <td><?php echo isset($row['email']) ? $row['email'] : ''; ?></td>
                            <td><?php echo isset($row['telephone']) ? $row['telephone'] : ''; ?></td>
                            <td><?php echo isset($row['role']) ? $row['role'] : ''; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else : ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
        <div id="services" class="page">
            <h1>Plans List</h1>
                    <?php if ($plans_result && $plans_result->num_rows > 0) : ?>
                        <table>
                            <tr>
                                <th>Plan ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Duration (days)</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                            <?php while ($row = $plans_result->fetch_assoc()) : ?>
                                <tr>
                                    <form method="POST" action="">
                                        <td><?php echo isset($row['plan_id']) ? $row['plan_id'] : ''; ?></td>
                                        <td><input type="text" name="plan_title" value="<?php echo isset($row['title']) ? $row['title'] : ''; ?>" style="color: black;"></td>
                                        <td><textarea name="plan_description" style="color: black;"><?php echo isset($row['description']) ? $row['description'] : ''; ?></textarea></td>
                                        <td><input type="number" name="plan_duration" value="<?php echo isset($row['duration']) ? $row['duration'] : ''; ?>" style="color: black;"></td>
                                        <td><input type="text" name="plan_price" value="<?php echo isset($row['price']) ? $row['price'] : ''; ?>" style="color: black;"></td>
                                        <td>
                                            <input type="hidden" name="plan_id" value="<?php echo isset($row['plan_id']) ? $row['plan_id'] : ''; ?>">
                                            <input type="submit" name="edit_plan" value="Save">
                                            <input type="submit" name="delete_plan" value="Delete" onclick="return confirm('Are you sure you want to delete this plan?');">
                                        </td>
                                    </form>
                                </tr>
                            <?php endwhile; ?>
                        </table>
            <?php else : ?>
                <p>No plans found.</p>
            <?php endif; ?>
            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>                  
            <?php if (!empty($success_message)) : ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

         <!-- Add Plan Form -->
         <h1>Add New Plan</h1>
                <form method="POST" action="">
                    <label for="plan_title">Title:</label>
                    <input type="text" name="plan_title" required><br>
                    <label for="plan_description">Description:</label>
                    <textarea name="plan_description" required></textarea><br>
                    <label for="plan_duration">Duration (days):</label>
                    <input type="number" name="plan_duration" required><br>
                    <label for="plan_price">Price:</label>
                    <input type="text" name="plan_price" required><br>
                    <input type="submit" name="add_plan" value="Add Plan">
                </form>

            </div>
            
            </div>
        </div>
    </div>

<script src="./js/script.js"></script>
</body>
</html>
