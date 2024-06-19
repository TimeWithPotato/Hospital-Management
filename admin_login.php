<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';


include './Admin/connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) {
    // echo "connected";
    $conn->begin_transaction();
    try {
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE email = 'arifmainuddin18@gmail.com'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        if($stmt->error){
            throw new Exception('Not Admin');
        }

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ($password == $row['password']) {

            function generateMail($send_to, $subject, $body)
            {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = "tls";
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->Username = "mohammed.mainuddin@northsouth.edu";
                $mail->Password = "gunb kwvl ppsi qize";
                $mail->setFrom("mohammed.mainuddin@northsouth.edu", "Arif Mainuddin");
                $mail->Subject = $subject;
                $mail->Body = $body;

                $mail->addAddress($send_to);
                $mail->send();
            }
            include './Admin/connect.php';
            $email = $row['email'];
            $userName = 'Arif';
            $password = $_POST['password'];


            // echo 'connected';
            // echo $email;
            // echo $userName;
            // echo $password;
            error_reporting(0);
            $send_to_email = $email;
            $verification_otp = random_int(100000, 999999);
            $send_to_name = $userName;
            $subject = "Your Activation";
            $body = "Assalamualaikum {$send_to_name}\n Enter this OTP {$verification_otp} to complete sign up\nThank You";

            generateMail($send_to_email, $subject, $body);

            $sql = "INSERT INTO otp_verification (email, password, otp, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $email, $password, $verification_otp);
            $stmt->execute();

            $conn->commit();
            echo "<script>alert('An OTP has sent to your mail. Check your inbox or mail');
    window.location.href='http://localhost/Hospital-Management/admin_otp_checker.php';
    </script>";

        } else {
            throw new Exception('You are not admin');
        }
    }catch(Exception $e){
        $conn->rollback();
        echo "<script>alert('".$e->getMessage()."');
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
                                <a class="nav-link" href="http://localhost/Hospital-Management/signup.php">Sign Up</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="http://localhost/Hospital-Management/Login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="http://localhost/Hospital-Management/admin_login.php">Admin</a>
                            </li>
                            <!-- <li class="nav-item">
                                <a class="nav-link" href="http://localhost/Hospital-Management/forgot_pass.php">Forgot Password</a>
                            </li> -->
                        </ul>
                    </div>
                </div>
            </nav>
        </section>
        <!-- navbar end -->

        <!-- Carousel start -->
        <section class="container carousel-container">
            <div id="carouselExample" class="carousel slide">
                <div class="carousel-inner container slider-bg-color p-5" style="background-color: rgb(145, 144, 144);">
                    <div class="carousel-item active ">
                        <div class="row d-flex flex-lg-row justify-content-around flex-column-reverse align-items-center">
                            <div class="col-lg-7 d-flex align-itmes-center mb-5">
                                <div class="carousel-article">
                                    <h1><span class="text-warning">Up To Date</span> Equipment</h1>
                                    <p class="mb-5">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Aut, nobis! <br> Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse, minima.</p>
                                    <button class="btn border rounded carousel-button">
                                        <a class="nav-link" href="#">Appoint a doctor now!</a>
                                    </button>
                                </div>
                            </div>
                            <div class="col-5">
                                <img class="img-thumbnail" src="assets/stethoscope.png" alt="...">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item ">
                        <div class="row d-flex flex-lg-row justify-content-around flex-column-reverse align-items-center">
                            <div class="col-lg-7 d-flex align-itmes-center mb-5">
                                <div class="carousel-article ">
                                    <h1><span class="text-warning">Accurate</span> Test & X-Ray</h1>
                                    <p class="mb-5">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Aut, nobis! <br> Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse, minima.</p>
                                    <button class="btn border rounded carousel-button">
                                        <a class="nav-link" href="#">Appoint a doctor now!</a>
                                    </button>
                                </div>
                            </div>
                            <div class="col-5">
                                <img class="img-thumbnail" src="assets/national-cancer-institute-mBrfAiw_CZA-unsplash.png" alt="...">
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="row d-flex flex-lg-row justify-content-around flex-column-reverse align-items-center">
                            <div class="col-lg-7 d-flex align-itmes-center mb-5">
                                <div class="carousel-article">
                                    <h1><span class="text-warning">Experienced</span> Doctor</h1>
                                    <p class="mb-5">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Aut, nobis! <br> Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse, minima.</p>
                                    <button class="btn border rounded carousel-button">
                                        <a class="nav-link" href="#">Appoint a doctor now!</a>
                                    </button>
                                </div>
                            </div>
                            <div class="col-5">
                                <img class="img-thumbnail" src="assets/operation_theatre (1).png" alt="...">
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev" style="padding:0px;">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next d-block" type="button" data-bs-target="#carouselExample" data-bs-slide="next" style="padding-right:100px;">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>
        <!-- Carousel end -->
    </header>

    <main>
        <section class="container">
            <form action="admin_login.php" method="POST" class="form-floating" style="margin-top:8vh;">
                <h1 class="text text-primary text-center fw-bold"><span class="fst-italic">A</span><span class="fst-italic fs-3 fw-bold text-danger">d</span>min Login</h1>
                <div class="form-floating mb-3">
                    <input name="password" type="password" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingText">Your Password</label>
                </div>
                <button class="btn btn-primary" type="submit" name="signup">SignUp</button>
            </form>
        </section>
    </main>
    <!-- Bootstrap script link -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>