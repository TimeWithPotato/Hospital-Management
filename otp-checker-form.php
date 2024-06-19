<?php
include './Admin/connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include './Admin/connect.php';
    // Check if the OTP exists in the database and is within the 3-minute window
    $sql = "SELECT * FROM otp_verification WHERE otp = ? AND created_at >= NOW() - INTERVAL 3 MINUTE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        
      echo "<script>alert('OTP Verified Successfully. Login with your password and email');
      window.location.href='http://localhost/Hospital-Management/Login.php';
      </script>";
      
      $user_email = $_POST['email'];
      $user_pass = $_POST['password'];
      $user_type = 'patient';
    $user_sql = "INSERT INTO user (email,password,type) VALUES(?,?,?)";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("sss", $user_email, $user_pass, $user_type);
    $user->execute();
    }else{
      echo "<script>alert('OTP doesn't match or time up. Signup again.');
      window.location.href='http://localhost/Hospital-Management/signup.php';
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
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="signup.php">Sign Up</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="Login.php">Login</a>
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
        <form action="otp-checker-form.php" method="POST" class="form-floating" style="margin-top:20vh;">
            <h1 class="text text-primary text-center fw-bold">OTP Verification</h1>
            <div class="form-floating mb-3">
                <input name="email" type="email" class="form-control" id="floatingInput" placeholder="Enter Email" required>
                <label for="floatingInput">Email</label>
            </div>
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