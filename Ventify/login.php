<?php
session_start();

// Include the database connection file
include('conn.php');

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form is a registration form
    if (isset($_POST["reg_username"]) && isset($_POST["reg_password"]) && isset($_POST["telephone"]) && isset($_POST["email"])) {
        // Registration process
        $reg_username = $_POST["reg_username"];
        $reg_password = $_POST["reg_password"];
        $telephone = $_POST["telephone"];
        $email = $_POST["email"];

        // Check if the username is already taken
        $check_query = "SELECT * FROM users WHERE username = '$reg_username'";
        $check_result = $conn->query($check_query);

        if ($check_result->num_rows == 0) {
            // Insert new user into the database
            $insert_query = "INSERT INTO users (username, password, telephone, email) VALUES ('$reg_username', '$reg_password', '$telephone', '$email')";
            $conn->query($insert_query);

            // Set success message for registration
            $success_message = "Registration successful!";
        } else {
            // Display an error message for duplicate username
            echo "Username already taken. Please choose another username.";
        }
    } else {
        // Check if the form is a login form
        if (isset($_POST["username"]) && isset($_POST["password"])) {
            // Login process
            $username = $_POST["username"];
            $password = $_POST["password"];
             header("Location:home.html");

            // Check if admin credentials
            if ($username === "venti" && $password === "0214") {
                // Redirect to admin.php
                header("Location: admin.php");
                exit();
            }

            // Check credentials using the database
            $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                // Start a session and store the username
                $_SESSION["username"] = $username;

                // Set success message for login
                $success_message = "Login successful!";
            } else {
                // Display an error message for invalid credentials
                echo "Invalid username or password";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">

    <title>Login Page</title>
    <link rel="stylesheet" href="./css/test_login.css">
</head>

<body>
    <div class="container">
        <div class="register-box">
            <h2 class="register-title">
                <span>No Have，Go</span>Register
            </h2>
            <?php
            if (!empty($success_message) && isset($_POST["reg_username"])) {
                echo "<p style='color: green;'>$success_message</p>";
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
                }
                ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-box">
                        <input type="text" id="username" name="username" required placeholder="Username">
                        <input type="password" id="password" name="password" required placeholder="Password">
                    </div>
                    <button type="submit" value="Login">Login</button>
                </form>
            </div>
        </div>
    </div>
    <script src="./js/test_login.js"></script>
</body>

</html>