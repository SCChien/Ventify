<?php
session_start();
include('./core/conn.php');

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $id_query = "SELECT id, pfp FROM users WHERE username = '$username'";
    $id_result = $conn->query($id_query);

    if ($id_result->num_rows == 1) {
        // Fetch the user's ID and avatar path
        $row = $id_result->fetch_assoc();
        $user_id = $row['id'];
        $avatarPath = $row['pfp'];
    } else {
        $avatarPath = 'default_avatar.jpg';
    }
} else {
    header("Location:login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Premium</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel="stylesheet" href="./css/pricing.css">
</head>
<body>

<section class="plans__container">
  <div class="plans">
    <div class="plansHero">
      <h1 class="plansHero__title">Ventify Premium</h1>
    </div>
    <div class="planItem__container">
      <div class="planItem planItem--free">

        <div class="card">
          <div class="card__header">
            <div class="img1"><img src="../Ventify/image/icon1.png"></div>
            <h2>Ventify Individual</h2>
          </div>
          <div class="card__desc">Best Plan for Personally</div>
        </div>

        <div class="price">RM3<span>/ month</span></div>

        <ul class="featureList">
          <li>Better Audio Quality</li>
          <li>No Ads</li>
          <li>Cancel Anytime</li>
          <li class="disabled">Up To 3 users</li>
        </ul>

        <a href="payment.php" class="button">Get Started</a>
    </div>
      <!--free plan ends -->

      <!--pro plan starts -->
      <div class="planItem planItem--pro">
        <div class="card">
          <div class="card__header">
            <div class="img1"><img src="../Ventify/image/icon1.png"></div>
            <h2>Student</h2>
            <div class="card__label label">Best Value</div>
          </div>
          <div class="card__desc">More Cheaper to Student</div>
        </div>

        <div class="price">RM2<span>/ month</span></div>

        <ul class="featureList">
            <li>Better Audio Quality</li>
            <li>No Ads</li>
            <li>Cancel Anytime</li>
            <li class="disabled">Up To 3 users</li>
          </ul>

          <a href="payment2.php" class="button">Get Started</a>
      </div>
      <!--pro plan ends -->

      <!--entp plan starts -->
      <div class="planItem planItem--entp">
        <div class="card">
          <div class="card__header">
            <div class="img1"><img src="../Ventify/image/icon1.png"></div>
            <h2>Family</h2>
          </div>
          <div class="card__desc">Better For Family</div>
        </div>

        <div class="price">RM4<span>/ month</span></div>

        <ul class="featureList">
            <li>Better Audio Quality</li>
            <li>No Ads</li>
            <li>Cancel Anytime</li>
            <li>Up To 3 users</li>
          </ul>
          <a href="payment3.php" class="button">Get Started</a>
      </div>
      <!--entp plan ends -->
    </div>

    <!-- Add this section to include the button that links to plans.php -->
    <div class="morePlans">
      <a href="plans.php" class="button">More Premium Plans</a>
    </div>

  </div>
</section>
<!-- partial -->
</body>
</html>
