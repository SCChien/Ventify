<?php
session_start();
include('./core/conn.php');

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $id_query = "SELECT id, pfp, role FROM users WHERE username = '$username'";
    $id_result = $conn->query($id_query);

    if ($id_result->num_rows == 1) {
        $row = $id_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['pfp'];
        $userRole = $row['role'];

        echo "<script>var userRole = '$userRole';</script>";
    } else {
        $avatarPath = 'default_avatar.jpg';
    }
} else {
    header("Location:login.php");
    exit();
}

// Fetch plans from the database
$plans_query = "SELECT plan_id, title, description, price FROM plans";
$plans_result = $conn->query($plans_query);
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <title>Plans</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="./css/plans.css">
</head>
<body>
<header>
  <a href="index.php"><img src="./image/icon_white.png"><span>entify</span></a>
</header>
<section class="plans__container">
  <div class="plans">
    <div class="plansHero">
      <h1 class="plansHero__title">Ventify Premium</h1>
    </div>
    <div class="planItem__container">
      <?php
      if ($plans_result->num_rows > 0) {
          while($plan = $plans_result->fetch_assoc()) {
              $plan_class = '';
              if ($plan['title'] == 'Family') {
                  $plan_class = 'planItem--entp';
              } elseif ($plan['title'] == 'Student') {
                  $plan_class = 'planItem--pro';
              } else {
                  $plan_class = 'planItem--free';
              }
              echo "<div class='planItem $plan_class'>";
              echo "<div class='card'>";
              echo "<div class='card__header'>";
              echo "<div class='img1'><img src='../Ventify/image/icon1.png'></div>";
              echo "<h2>" . $plan['title'] . "</h2>";
              if ($plan['title'] == 'Student') {
                  echo "<div class='card__label label'>Best Value</div>";
              }
              echo "</div>";
              echo "<div class='card__desc'>" . $plan['description'] . "</div>";
              echo "</div>";
              echo "<div class='price'>RM" . $plan['price'] . "<span>/ month</span></div>";
              echo "<form action='planspay.php' method='POST'>";
              echo "<input type='hidden' name='plan_id' value='" . $plan['plan_id'] . "'>";
              echo "<input type='hidden' name='plan_price' value='" . $plan['price'] . "'>";
              echo "<button type='submit' class='button'>Get Started</button>";
              echo "</form>";
              echo "</div>";
          }
      } else {
          echo "<p>No plans available.</p>";
      }
      ?>
    </div>
    

    <div class="morePlans">
      <a href="premium.php" class="button">Back</a>
    </div>
  </div>
</section>
</body>
</html>
