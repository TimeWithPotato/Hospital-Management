<?php
include ('../Admin/connect.php');
// echo htmlspecialchars($_POST['doc_id']);
// echo htmlspecialchars($_POST['email']);
// echo htmlspecialchars($_POST['password']);
if(isset($_POST['doc_id']) && isset($_POST['email']) && isset($_POST['password'])){
    $conn->begin_transaction();
    try{
        $password = $_POST['password'];
        $email = $_POST['email'];
        $doc_id = $_POST['doc_id'];
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $password, $email);
        $stmt->execute();
        if($stmt->error){
            throw new Exception('Cannot update, email does not exist');
        }

        $sql = "UPDATE doctor SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $password, $email);
        $stmt->execute();
        if($stmt->error){
            throw new Exception('Cannot update, email or doc id does not exist');
        }
        echo " <script> alert('Password Updated');
        window.location.href = 'http://localhost/Hospital-Management/user/doctor.php';
        </script>";
        $conn->commit();
    }catch(Exception $e){
        $conn->rollback();
        echo " <script> alert(' " . $e->getMessage() . "');
        window.location.href = 'http://localhost/Hospital-Management/user/doctor.php';
        </script>";
    }
}

//Patient
if(isset($_POST['pat_id']) && isset($_POST['pat_email']) && isset($_POST['pat_password'])){
    $conn->begin_transaction();
    try{
        $password = $_POST['pat_password'];
        $email = $_POST['pat_email'];
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $password, $email);
        $stmt->execute();
        if($stmt->error){
            throw new Exception('Cannot update, email does not exist');
        }
        echo " <script> alert('Password Updated');
        window.location.href = 'http://localhost/Hospital-Management/user/patient.php';
        </script>";
        $conn->commit();
    }catch(Exception $e){
        $conn->rollback();
        echo " <script> alert(' " . $e->getMessage() . "');
        window.location.href = 'http://localhost/Hospital-Management/user/patient.php';
        </script>";
    }
}
//General Patient
if(isset($_POST['gen_pat_email']) && isset($_POST['gen_pat_password'])){
    $conn->begin_transaction();
    try{
        $password = $_POST['gen_pat_password'];
        $email = $_POST['gen_pat_email'];
        $sql = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $password, $email);
        $stmt->execute();
        if($stmt->error){
            throw new Exception('Cannot update, email does not exist');
        }
        echo " <script> alert('Password Updated');
        window.location.href = 'http://localhost/Hospital-Management/user/general_patient.php';
        </script>";
        $conn->commit();
    }catch(Exception $e){
        $conn->rollback();
        echo " <script> alert(' " . $e->getMessage() . "');
        window.location.href = 'http://localhost/Hospital-Management/user/general_patient.php';
        </script>";
    }
}