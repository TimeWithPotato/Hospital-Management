<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
function generateMail($send_to,$subject,$body){
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
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['signup'])){
include './Admin/connect.php';
    $email = $_POST['email'];
    $userName = $_POST['username'];
    $password = $_POST['password'];
    $type = 'patient';
    $sql = "INSERT INTO users (email,password,type) VALUES (?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $password, $type);
    $stmt->execute();


// echo 'connected';
// echo $email;
// echo $userName;
// echo $password;
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


    echo "<script>alert('An OTP has sent to your mail. Check your inbox or mail');
    window.location.href='http://localhost/Hospital-Management/otp-checker-form.php';
    </script>";

}
