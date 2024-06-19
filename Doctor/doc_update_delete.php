<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include ('../Admin/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $doc_id = $_POST['doc_id'];
    $specialities = $_POST['specialities'];
    $degrees = $_POST['degrees'];
    $institutes = $_POST['institutes'];
    $fees = $_POST['fees'];
    $phone_numbers = $_POST['phone_number'];

    // Update fees
    $sql = "UPDATE doctor SET fees = ? WHERE doc_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ds", $fees, $doc_id);
    mysqli_stmt_execute($stmt);

    // Update specialties
    $sql = "DELETE FROM doc_speciality WHERE doc_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $doc_id);
    mysqli_stmt_execute($stmt);

    $specialitiesArray = explode(",", $specialities);
    foreach ($specialitiesArray as $speciality) {
        $sql = "INSERT INTO doc_speciality (doc_id, specialty) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $doc_id, $speciality);
        mysqli_stmt_execute($stmt);
    }

    // Update degrees and institutes
    $sql = "DELETE FROM doc_degree WHERE doc_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $doc_id);
    mysqli_stmt_execute($stmt);

    $degreesArray = explode(",", $degrees);
    $institutesArray = explode(",", $institutes);
    for ($i = 0; $i < count($degreesArray); $i++) {
        $sql = "INSERT INTO doc_degree (doc_id, degree_name, institute) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $doc_id, $degreesArray[$i], $institutesArray[$i]);
        mysqli_stmt_execute($stmt);
    }

    // Update phone numbers
    $sql = "DELETE FROM doc_phone_num WHERE doc_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $doc_id);
    mysqli_stmt_execute($stmt);

    $phoneNumbersArray = explode(",", $phone_numbers);
    foreach ($phoneNumbersArray as $phoneNumber) {
        $sql = "INSERT INTO doc_phone_num (doc_id, phone_num) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $doc_id, $phoneNumber);
        mysqli_stmt_execute($stmt);
    }

    // Redirect back to the main page after update
    header("Location: http://localhost/Hospital-Management/Doctor/doctor.php");
    exit();
}

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $doc_id = $_POST['doc_id'];
    if (!empty($doc_id)) {
        // Begin transaction
        mysqli_begin_transaction($conn);

        try {
            // // Delete related data from doc_speciality table
            // $sql = "DELETE FROM doc_speciality WHERE doc_id = ?";
            // $stmt = mysqli_prepare($conn, $sql);
            // mysqli_stmt_bind_param($stmt, "s", $doc_id);
            // mysqli_stmt_execute($stmt);

            // // Delete related data from doc_degree table
            // $sql = "DELETE FROM doc_degree WHERE doc_id = ?";
            // $stmt = mysqli_prepare($conn, $sql);
            // mysqli_stmt_bind_param($stmt, "s", $doc_id);
            // mysqli_stmt_execute($stmt);

            // // Delete related data from doc_phone_num table
            // $sql = "DELETE FROM doc_phone_num WHERE doc_id = ?";
            // $stmt = mysqli_prepare($conn, $sql);
            // mysqli_stmt_bind_param($stmt, "s", $doc_id);
            // mysqli_stmt_execute($stmt);

            // Delete doctor from doctor table
            $sql = "DELETE FROM doctor WHERE doc_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $doc_id);
            mysqli_stmt_execute($stmt);

            // Commit transaction
            mysqli_commit($conn);

            // Redirect back to the main page after delete
            header("Location: http://localhost/Hospital-Management/Doctor/doctor.php");
            exit();
        } catch (mysqli_sql_exception $exception) {
            // Rollback transaction on error
            mysqli_rollback($conn);

            // Display error message (or handle it as necessary)
            echo "<script>alert('Failed to delete doctor:'". $exception->getMessage()." ')</script> " ;
        }
    }
}

$conn->close();
