<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include '../Admin/connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pat_id'])) {
    $pat_id = $_POST['pat_id'];

    if (!empty($pat_id)) {
        mysqli_begin_transaction($conn);

        try {
            $sql = "DELETE FROM patient WHERE pat_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $pat_id);
            mysqli_stmt_execute($stmt);

            mysqli_commit($conn);

            header("Location: http://localhost/Hospital-Management/General Patient/gen_patient.php");
            exit();
        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($conn);
            echo "<script>alert('Failed to delete patient: " . $exception->getMessage() . "')</script>";
            exit();
        }
    }
}
$conn->close();
