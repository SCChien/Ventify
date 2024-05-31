<?php
session_start(); 

include('./core/conn.php');
require_once('stripe-php/init.php'); // Stripe PHP

// 设置你的秘钥
\Stripe\Stripe::setApiKey('sk_test_51NfeE7HgfdT34xcjJ67RLOiUBcjUfuFWhsfwOk7wo8fOzJ1xcq3cLwSudqnhzrF6LIC9NPWCaMAjKNfF9A4Dv3FT00xgwlMbRP');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_number = $_POST['card_number'];
    $expiry_month = $_POST['expiry_month'];
    $expiry_year = $_POST['expiry_year'];
    $cvc = $_POST['cvc'];

    try {
        $charge = \Stripe\Charge::create([
            'amount' => 300, 
            'currency' => 'MYR',
            'description' => 'Ventify Premium Individual',
            'source' => 'tok_visa', // token for test
        ]);
    
        $username = $_SESSION['username'];
    
        // Update user's role
        $sql_update_role = "UPDATE users SET role='VIP Individual' WHERE username='$username'";
        if ($conn->query($sql_update_role) === TRUE) {
            // Insert payment record
            $sql_insert_payment = "INSERT INTO payment (user_id, amount) VALUES ((SELECT id FROM users WHERE username='$username'), 3)";
            if ($conn->query($sql_insert_payment) === TRUE) {
                echo "<script>alert('Payment successful! You are now a VIP member.'); window.location.href = 'index.php';</script>";
            } else {
                echo "Error inserting payment record: " . $conn->error;
            }
        } else {
            echo "Error updating user's role: " . $conn->error;
        }
    } catch (\Stripe\Exception\CardException $e) {
        echo 'Payment failed. ' . $e->getError()->message;
    }    
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./css/payment.css"> 
    <title>Stripe Example</title>
    <meta charset="UTF-8" />
</head>
<body>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <p>Ventify Premium Individual</p>
    <p><strong>MYR 3</strong></p>
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

    <button type="submit">Pay</button>
</form>

</body>
</html>
