<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include('./core/conn.php');

// 处理邮件发送逻辑
if(isset($_POST['submit'])){
    $to = $_POST['to'];

    $sql = "SELECT * FROM users WHERE email = '$to'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // 如果存在匹配的记录，发送邮件
        $subject = "Password Reset";
        $message = '<html><body>';
        $message .= '<h1>Password Reset</h1>';
        $message .= '<p>To reset your password, click the link below:</p>';
        $message .= '<p><a href="http://localhost/Ventify/rspass.php?email=' . urlencode($to) . '">Reset Password</a></p>';
        $message .= '</body></html>';
        
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ddgoh46@gmail.com';
            $mail->Password = 'qogl uwyw zxxz ffcq'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('your_email@gmail.com', 'Kelala');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();
            echo 'Email sent successfully';
        } catch (Exception $e) {
            echo "Failed to send email. Error: {$mail->ErrorInfo}";
        }
    } else {
        // 如果数据库中不存在匹配的记录，显示消息给用户
        echo "Email address not found in database";
    }

    $conn->close(); // 关闭数据库连接
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/email.css">
    <title>Password Reset</title>
</head>
<body>
    <h1>Password Reset</h1>
    <form method="post">
        <p>
            <label for="to">TO:</label>
            <input type="text" name="to" placeholder="Enter your email address"/>
        </p>
        <button type="submit" name="submit">Send Reset Link</button>
    </form>
</body>
</html>
