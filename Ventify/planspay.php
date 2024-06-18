<?php
session_start();
include('./core/conn.php');
require_once('stripe-php/init.php'); // Stripe PHP

// 设置你的秘钥
\Stripe\Stripe::setApiKey('sk_test_51NfeE7HgfdT34xcjJ67RLOiUBcjUfuFWhsfwOk7wo8fOzJ1xcq3cLwSudqnhzrF6LIC9NPWCaMAjKNfF9A4Dv3FT00xgwlMbRP');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$id_query = "SELECT id, pfp, role FROM users WHERE username = '$username'";
$id_result = $conn->query($id_query);

if ($id_result->num_rows == 1) {
    $row = $id_result->fetch_assoc();
    $user_id = $row['id'];
    $avatarPath = $row['pfp'];
    $userRole = $row['role'];
} else {
    $avatarPath = 'default_avatar.jpg';
}

if (isset($_POST['plan_id'])) {
    $plan_id = $_POST['plan_id'];
    $plan_price = $_POST['plan_price'];

    // 確保計劃價格為數字
    $plan_price = floatval($plan_price);

    // 获取计划的标题
    $plan_query = "SELECT title FROM plans WHERE plan_id = '$plan_id'";
    $plan_result = $conn->query($plan_query);
    if ($plan_result->num_rows == 1) {
        $plan = $plan_result->fetch_assoc();
        $plan_title = $plan['title'];
    } else {
        echo "Plan not found.";
        exit();
    }
} else {
    echo "No plan selected.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['card_number'])) {
    $card_number = $_POST['card_number'];
    $expiry_month = $_POST['expiry_month'];
    $expiry_year = $_POST['expiry_year'];
    $cvc = $_POST['cvc'];

    try {
        $charge = \Stripe\Charge::create([
            'amount' => $plan_price * 100, // 金额单位为 cents
            'currency' => 'MYR',
            'description' => 'Ventify Premium Plan',
            'source' => 'tok_visa', // token for test
        ]);

        // Update user's role
        $update_role_query = "UPDATE users SET role = '$plan_title' WHERE username = '$username'";

        // Insert payment record
        $insert_payment_query = "INSERT INTO payment (user_id, amount, payment_date, plan_id) VALUES ('$user_id', '$plan_price', NOW(),'$plan_id')";

        if ($conn->query($update_role_query) === TRUE && $conn->query($insert_payment_query) === TRUE) {
            echo "<script>alert('Payment successful! You are now a VIP member.'); window.location.href = 'index.php';</script>";
            exit();
        } else {
            echo "更新角色或插入付款記錄時出錯: " . $conn->error;
            exit();
        }
    } catch (\Stripe\Exception\CardException $e) {
        echo 'Payment failed. ' . $e->getError()->message;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./css/planspay.css">
    <title>Payment</title>
</head>
<body>
<header>
  <a href="index.php"><img src="./image/icon_white.png"><span>entify</span></a>
</header>
<div class="back">
    <h3></h3>
</div>
    <p>您選擇的計劃：</p>
    <h2><?php echo htmlspecialchars($plan_title); ?></h2>
    <p>價格：$<?php echo htmlspecialchars($plan_price); ?></p>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="plan_id" value="<?php echo htmlspecialchars($plan_id); ?>">
        <input type="hidden" name="plan_price" value="<?php echo htmlspecialchars($plan_price); ?>">
        <div>
            <label for="card-number">Card Number:</label>
            <input type="text" id="card-number" name="card_number" required>
        </div>
        <div>
            <label for="expiry-month">Expiry Month:</label>
            <input type="text" id="expiry-month" name="expiry_month" required>
        </div>
        <div>
            <label for="expiry-year">Expiry Year:</label>
            <input type="text" id="expiry-year" name="expiry_year" required>
        </div>
        <div>
            <label for="cvc">CVC:</label>
            <input type="text" id="cvc" name="cvc" required>
        </div>

        <button type="submit">確認付款</button>
    </form>
</body>
</html>
