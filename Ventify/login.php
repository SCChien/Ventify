<?php
session_start();

// Include the database connection file
include('./core/conn.php');

$success_message = "";
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["reg_username"]) && isset($_POST["reg_password"]) && isset($_POST["telephone"]) && isset($_POST["email"])) {
        // Registration process
        $reg_username = $_POST["reg_username"];
        $reg_password = $_POST["reg_password"];
        $telephone = $_POST["telephone"];
        $email = $_POST["email"];

        if (strlen($reg_password) < 6) {
            $error_message = "Passwords cannot be less than 6 characters.";
        } else {
            // Hash the password before storing
            $hashed_password = password_hash($reg_password, PASSWORD_DEFAULT);

            // Check if the username or email is already taken
            $check_query = "SELECT * FROM users WHERE username = '$reg_username' OR email = '$email'";
            $check_result = $conn->query($check_query);

            if ($check_result->num_rows == 0) {
                // Insert new user into the database
                $insert_query = "INSERT INTO users (username, password, telephone, email, role) VALUES ('$reg_username', '$hashed_password', '$telephone', '$email', 'NORMAL USER')";
                if ($conn->query($insert_query) === TRUE) {
                    // Set success message for registration
                    $success_message = "Registration successful!";
                } else {
                    // Display an error message for database error
                    $error_message = "Error: " . $conn->error;
                }
            } else {
                // Check if the duplicate is due to username or email
                $row = $check_result->fetch_assoc();
                if ($row['username'] == $reg_username) {
                    $error_message = "Username already taken. Please choose another username.";
                } else {
                    $error_message = "Email already registered. Please use another email.";
                }
            }
        }
    } else {
        // Check if the form is a login form
        if (isset($_POST["username"]) && isset($_POST["password"])) {
            // Login process
            $username = $_POST["username"];
            $password = $_POST["password"];

            // Check if user is "venti" and password is "0214"
            if ($username === "venti" && $password === "0214") {
                // Set session for venti
                $_SESSION["username"] = $username;
                // Redirect to admin.php
                header("Location: admin.php");
                exit();
            }

            // Check credentials using the database for users
            $user_query = "SELECT * FROM users WHERE username = '$username'";
            $user_result = $conn->query($user_query);

            if ($user_result->num_rows > 0) {
                $user = $user_result->fetch_assoc();
                // Verify the hashed password
                if (password_verify($password, $user['password'])) {
                    // Start a session and store the username
                    $_SESSION["username"] = $username;

                    // Set success message for login
                    $success_message = "Login successful!";
                    // Redirect user to index.php
                    header("Location: index.php");
                    exit(); // Ensure that script execution stops after redirection
                } else {
                    // Display an error message for invalid credentials
                    $error_message = "Invalid username or password";
                }
            } else {
                // Check credentials using the database for admins
                $admin_query = "SELECT * FROM admin WHERE username = '$username'";
                $admin_result = $conn->query($admin_query);
                
                if ($admin_result->num_rows > 0) {
                    $admin = $admin_result->fetch_assoc();
                    // Verify the hashed password
                    if (password_verify($password, $admin['password'])) {
                        // Start a session and store the username
                        $_SESSION["username"] = $username;

                        // Set success message for login
                        $success_message = "Login successful!";
                        // Redirect admin to admin.php
                        header("Location: admin.php");
                        exit(); // Ensure that script execution stops after redirection
                    } else {
                        // Display an error message for invalid credentials
                        $error_message = "Invalid username or password for admin";
                    }
                } else {
                    $error_message = "Invalid username or password";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="icon" href="image/logo.ico" type="image/x-icon">
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
<header>
  <a href="#"><img src="./image/icon_white.png"><span>entify</span></a>
</header>
<div class="back">
    <h3></h3>
</div>
<div class="container" >
        <div class="register-box">
            <h2 class="register-title">
                <span>No Have,Go</span>Register
            </h2>
            <?php
            if (!empty($success_message) && isset($_POST["reg_username"])) {
                echo "<p style='color: green;'>$success_message</p>";
            } elseif (!empty($error_message) && isset($_POST["reg_username"])) {
                echo "<p style='color: red;'>$error_message</p>";
            }
            ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-box">
                <input type="text" id="reg_username" name="reg_username" required placeholder="Username">
                <input type="text" id="telephone" name="telephone" required placeholder="Telephone">
                <input type="text" id="email" name="email" required placeholder="Email">
                <input type="password" id="reg_password" name="reg_password" required placeholder="Password">
                <input type="password" placeholder="Confirm Password">
            </div>
            <button type="submit" value="Register">Register</button>
            </form>
        </div>
        <div class="login-box slide-up">
            <div class="center">
                <h2 class="login-title">
                    <span>Already Have Account?</span>Login
                </h2>
                <?php
                if (!empty($success_message) && isset($_POST["username"])) {
                    echo "<p style='color: green;'>$success_message</p>";
                } elseif (!empty($error_message) && isset($_POST["username"])) {
                    echo "<p style='color: red;'>$error_message</p>";
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-box">
                        <input type="text" id="username" name="username" required placeholder="Username">
                        <input type="password" id="password" name="password" required placeholder="Password">
                    </div>
                    <button type="submit" value="Login">Login</button>
                    <p><a href ="email.php">Forget Password?</a></p>
                </form>
            </div>
        </div>
    </div>
    <div>
    <a href="index.html">
    <p>
    </a>
    </div>
    <script src="./js/login.js"></script>
</body>
</html>