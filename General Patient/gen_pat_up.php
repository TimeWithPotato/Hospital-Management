<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include ('../Admin/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $pat_id = $_POST['pat_id'];

    // Extract other form fields
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $phone_numbers = $_POST['phone_numbers']; // Array of phone numbers

    // Update patient details
    $update_patient_sql = "UPDATE patient SET f_name = ?, l_name = ? WHERE pat_id = ?";
    $stmt = $conn->prepare($update_patient_sql);
    $stmt->bind_param("sss", $f_name, $l_name, $pat_id);
    $stmt->execute();

    // Update phone numbers
    // First delete existing phone numbers for the patient
    $delete_phone_numbers_sql = "DELETE FROM pat_phone_num WHERE pat_id = ?";
    $stmt = $conn->prepare($delete_phone_numbers_sql);
    $stmt->bind_param("s", $pat_id);
    $stmt->execute();

    // Then insert the updated phone numbers
    $insert_phone_number_sql = "INSERT INTO pat_phone_num (pat_id, phone_num) VALUES (?, ?)";
    $stmt = $conn->prepare($insert_phone_number_sql);
    foreach ($phone_numbers as $phone_number) {
        $stmt->bind_param("ss", $pat_id, $phone_number);
        $stmt->execute();
    }

    echo "<script>alert('Patient details updated successfully.')
    window.location.href='http://localhost/Hospital-Management/General patient/gen_patient.php';
    </script>";
} else {
    echo "<script>alert('No patient ID specified...');
    window.location.href='http://localhost/Hospital-Management/General patient/gen_patient.php';
    </script>";
    exit();
}
$conn->close();