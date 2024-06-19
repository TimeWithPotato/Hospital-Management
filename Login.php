<?php
include('./Admin/connect.php');
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    
    // Get email and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute SQL query to check user credentials
    $sql = "SELECT * FROM users WHERE email = ? AND password = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // If a user with matching credentials is found
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch user data

        session_start(); // Start the session

        // Determine user type and set session variables accordingly
        if ($row['type'] == 'doctor') {
            $get_doc_id_sql = "SELECT doc_id FROM doctor WHERE email = ? LIMIT 1";
            $stmt = $conn->prepare($get_doc_id_sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $doc_id_result = $stmt->get_result();
            if ($doc_id_result->num_rows > 0) {
                $doc_id_row = $doc_id_result->fetch_assoc();
                $_SESSION['doc_id'] = $doc_id_row['doc_id'];
            }

            // Redirect to the doctor's page
            header('Location: http://localhost/Hospital-Management/user/doctor.php');
            exit(); // Ensure the script stops executing after redirection

        } else if ($row['type'] == 'patient') {
            $get_pat_id_sql = "SELECT pat_id FROM patient WHERE email = ? LIMIT 1";
            $stmt = $conn->prepare($get_pat_id_sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $pat_id_result = $stmt->get_result();
            if ($pat_id_result->num_rows > 0) {
                $pat_id_row = $pat_id_result->fetch_assoc();
                $_SESSION['pat_id'] = $pat_id_row['pat_id'];
                            // Redirect to the patient's page
            header('Location: http://localhost/Hospital-Management/user/patient.php');
            exit(); // Ensure the script stops executing after redirection
            } else {
                // If patient record does not exist, just store the email
                $_SESSION['email'] = $email;
            // Redirect to the general patient's page
            header('Location: http://localhost/Hospital-Management/user/general_patient.php');
            exit(); // Ensure the script stops executing after redirection
            }

        } else {
            echo "<script>alert('Information does not exist');</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
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
                <a class="nav-link" aria-current="page" href="index.html">Home</a>
              </li>
              <li class="nav-item">
                            <a class="nav-link" href="http://localhost/Hospital-Management/signup.php">Sign Up</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="http://localhost/Hospital-Management/Login.php">Login</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="http://localhost/Hospital-Management/admin_login.php">Admin</a>
                          </li>
            </ul>
          </div>
        </div>
      </nav>
    </section>
    <!-- navbar end -->
  </header>

  <!-- Main start -->
  <main style="margin-top: 50px;">
    <section class="container">
      <h1 class="text-center text-center text-success">Login to DoctorBari</h1>
      <div class="row">
        <div class="col-md-6 d-flex flex-column justify-content-center align-items-center">
          <h3 class="my-3">Please Enter your email and password</h3>
          <form method="POST">
            <div class="form-floating mb-3">
              <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="email" required>
              <label for="floatingInput">Email address</label>
            </div>
            <div class="form-floating mb-3">
              <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password" required>
              <label for="floatingPassword">Password</label>
            </div>
            <input class="btn btn-primary" type="submit">
          </form>
        </div>
        <div class="col-md-6">
          <img class="img-fluid" src="assets/signup.png" alt="">
        </div>
      </div>
    </section>

    <section style=" width:20% ;margin-top:-30.5em; margin-left:20.3em;">
      <!-- <?php
            if ($login) {
              //   echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
              //   <strong>Success</strong>
              //   <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              // </div>'

            }
            ?> -->
    </section>

  </main>
  <!-- Main end -->
  <footer class="container text-center footer-position">
    <p><small>Developed By <i><b>Najifa</b></i> <span class="text-warning">&</span> <i><b>Arif</b></i></small></p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>