<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
include './Admin/connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  try {
    include './Admin/connect.php';

    $conn->begin_transaction();
    $otp = $_POST['otp'];

    // Check if the OTP exists in the database and is within the 3-minute window
    $sql = "SELECT * FROM otp_verification WHERE otp = ? AND created_at >= NOW() - INTERVAL 3 MINUTE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
    $email = 'arifmainuddin18@gmail.com';
      session_start();
      $_SESSION['email']=$email;
        echo "<script>alert('OTP verified successfully!');
        window.location.href='http://localhost/Hospital-Management/homepage.php';
        </script>";
    } else {
      throw new Exception('Invalid or expired OTP! Please try again');
    
    }
    $conn->commit();
  }catch(Exception $e){
    $conn->rollback();
    echo "<script>alert('" . $e->getMessage() . " ');
    window.location.href='http://localhost/Hospital-Management/index.html';
    </script>";
  }

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoctorBair</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
           <!-- navbar start -->
            <section class="container">
                <nav class="navbar fixed-top navbar-expand-lg nav-body">
                    <div class="container">
                      <a class="navbar-brand" href="index.html">
                         <span class="doctor">Doctor</span>Bari
                      </a>
                      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                      </button>
                      <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                          <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.html">Home</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="signup.php">Sign Up</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="Login.php">Login</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="admin_login.php">Login</a>
                          </li>
                          <!-- <li class="nav-item">
                            <a class="nav-link" href="forgot_pass.php">Forgot Password</a>
                          </li> -->
                        </ul>
                      </div>
                    </div>
                  </nav>
            </section>
           <!-- navbar end -->
    </header>
    <main>
    <section class="container">
        <form action="admin_otp_checker.php" method="POST" class="form-floating" style="margin-top:20vh;">
            <h1 class="text text-primary text-center fw-bold">OTP Verification</h1>
            <div class="form-floating mb-3">
                <input name="otp" type="number" class="form-control" id="floatingInput" placeholder="Enter OTP" required>
                <label for="floatingInput">Enter OTP</label>
            </div>
            <button class="btn btn-primary" type="submit">Verify OTP</button>
        </form>
    </section>
</main>
</body>
</html>