<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include ('../Admin/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    $pat_id = $_POST['pat_id'];
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $phone_numbers = $_POST['phone_numbers'];
    $date_str = isset($_POST['date_of_release']) ? $_POST['date_of_release'] : "";

    if (!empty($pat_id)) {
        // Update patient details
        $update_patient_sql = "UPDATE patient SET f_name = ?, l_name = ? WHERE pat_id = ?";
        $stmt = $conn->prepare($update_patient_sql);
        $stmt->bind_param("sss", $f_name, $l_name, $pat_id);
        $stmt->execute();

        // Delete existing phone numbers for the patient
        $delete_phone_numbers_sql = "DELETE FROM pat_phone_num WHERE pat_id = ?";
        $stmt = $conn->prepare($delete_phone_numbers_sql);
        $stmt->bind_param("s", $pat_id);
        $stmt->execute();

        // Insert updated phone numbers
        $insert_phone_number_sql = "INSERT INTO pat_phone_num (pat_id, phone_num) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_phone_number_sql);
        foreach ($phone_numbers as $phone_number) {
            $stmt->bind_param("ss", $pat_id, $phone_number);
            $stmt->execute();
        }

        // Update Date of Release of Admission
        if (!empty($date_str)) {
            $date_obj = DateTime::createFromFormat('Y-m-d', $date_str);
            $date_of_release = $date_obj->format('Y-m-d');
            $update_date_of_release_sql = "UPDATE adm_pat SET date_of_release = ? WHERE pat_id = ?";
            $stmt = $conn->prepare($update_date_of_release_sql);
            $stmt->bind_param("ss", $date_of_release, $pat_id);
            $stmt->execute();
        }

        header("Location: http://localhost/Hospital-Management/Admitted-patient/adm_patient.php");
        echo "<script>alert('Patient details updated successfully.')</script>";
    } else {
        echo "<script>alert('No patient ID specified.')</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid request.')</script>";
    exit();
}

$conn->close();