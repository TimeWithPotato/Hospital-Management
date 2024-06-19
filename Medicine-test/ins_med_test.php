<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include('../Admin/connect.php');

//Retrieve pres id
$pres_id = '';
$sql = 'SELECT pres_id FROM prescription ORDER BY pres_id DESC LIMIT 1';
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
    $last_id = $row['pres_id'];
    $numeric_part = intval(substr($last_id, 4)) + 1;
    $pres_id = 'pres' . $numeric_part;
}else{
    $pres_id = 'pres1';
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    $pat_id = $_POST['pat_id'];
    $doc_id = $_POST['doc_id'];
    $fees;
    $date_str = $_POST['date'];
    $date_obj = DateTime::createFromFormat('Y-m-d', $date_str);
    $date = $date_obj->format('Y-m-d');
    $medication_details = $_POST['medication_details'];
    $medicines = isset($_POST['medicines']) ? $_POST['medicines'] : [];
    $med_quantity = isset($_POST['med_quantity']) ? $_POST['med_quantity'] : [];
    $tests = isset($_POST['tests']) ? $_POST['tests'] : [];

    // Check if the patient ID exists
    $check_pat_sql = "SELECT * FROM patient WHERE pat_id = ?";
    $stmt = $conn->prepare($check_pat_sql);
    $stmt->bind_param("s", $pat_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<script>
        alert('Patient ID does not exist.');
        window.location.href = 'http://localhost/Hospital-Management/Medicine-test/ins_med_test.php';
        </script>";
        exit();
    }
// Check if the doctor ID exists
$check_doc_sql = "SELECT * FROM doctor WHERE doc_id = ?";
$stmt = $conn->prepare($check_doc_sql);
$stmt->bind_param("s", $doc_id); 
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Doctor ID does not exist.";
    exit();
} else {
    $row = $result->fetch_assoc();
    $fees = $row['fees'];
}


    //Insert the pres_id and doctor id into prescription
    $insert_pres_doc_sql = "INSERT INTO prescription(pres_id,fees,date,medication_details,doc_id,pat_id) VALUES(?,?,?,?,?,?)";
    $stmt=$conn->prepare($insert_pres_doc_sql);
    $stmt->bind_param("sissss",$pres_id,$fees,$date,$medication_details,$doc_id,$pat_id );
    $stmt->execute();
    // Insert medicines into med_pres
    foreach ($medicines as $medicine) {
        $quantity = $med_quantity[$medicine];

        $insert_med_pres_sql = "INSERT INTO med_pres (pres_id, med_name, med_num) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_med_pres_sql);
        $stmt->bind_param("ssi", $pres_id, $medicine, $quantity);
        $stmt->execute();
    }

    // Insert tests into test_pres
    foreach ($tests as $test) {
        $insert_test_pres_sql = "INSERT INTO test_pres (pres_id, test_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_test_pres_sql);
        $stmt->bind_param("ss", $pres_id, $test);
        $stmt->execute();
    }

    echo "<script>
    alert('Medicines and tests inserted successfully.');
    window.location.href = 'http://localhost/Hospital-Management/Medicine-test/ins_med_test.php';
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Medicines and Tests</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
</head>

<body>
<header>
        <div class="w-75 mx-auto my-3">
            <nav class="navbar fixed-top navbar-expand-lg nav-body">
                <div class="container-fluid">
                    <a class="navbar-brand" href="http://localhost/Hospital-Management/homepage.php">DoctorBari</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Doctor
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-item">
                                        <a class="nav-link active" aria-current="page"
                                            href="http://localhost/Hospital-Management/Doctor/doctor.php">Doctor</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="http://localhost/Hospital-Management/Doctor/doc_insert.php">Doctor
                                            Insert</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Patient
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item"
                                            href="http://localhost/Hospital-Management/Admitted-patient/adm_patient.php">Admitted
                                            Patient</a>
                                    </li>
                                    <li><a class="dropdown-item"
                                            href="http://localhost/Hospital-Management/General patient/gen_patient.php">General
                                            Patient</a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="http://localhost/Hospital-Management/pat_registration.php">Patient
                                            Registration</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="http://localhost/Hospital-Management/financial/billing.php">Billing</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="http://localhost/Hospital-Management/financial/payment.php">Payment</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="http://localhost/Hospital-Management/financial/intallments.php">Installment
                                            Details</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="http://localhost/Hospital-Management/Financial/pay_installment.php">Pay Installment</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Prescription
                                </a>
                                <ul class="dropdown-menu">
                                   <li class="dropdown-item">
                                      <a class="nav-link" aria-current="page"
                                      href="http://localhost/Hospital-Management/Medicine-test/ins_med_test.php">New Prescription</a>
                                    </li>
                                   <li class="dropdown-item">
                                      <a class="nav-link" aria-current="page"
                                      href="http://localhost/Hospital-Management/Medicine-test/update_med_test.php">Update Prescription</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="http://localhost/Hospital-Management/logout.php">Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main style="margin-top:10vh">
        <section class="container">
            <h1 class="text-center">Insert New Prescription Form</h1>
            <form action="ins_med_test.php" class="form-floating mb-3" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" name="pat_id" required>
                    <label for="floatingInput">Patient ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" name="doc_id" required>
                    <label for="floatingInput">Doctor ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control text-danger fw-semibold fs-5" id="floatingInput" name="pres_id" value="<?php echo htmlspecialchars($pres_id); ?>" required>
                    <label for="floatingInput">Prescription ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="date" class="form-control" id="floatingInput" name="date" required>
                    <label for="floatingInput">Date</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" name="medication_details" required>
                    <label for="floatingInput">Medication Details</label>
                </div>
                <div class="mb-3">
                    <label for="medicines" class="form-label">Medicines</label>
                    <?php
                    // Fetch available medicines from the database
                    $medicines_sql = "SELECT med_name FROM medicine";
                    $medicines_result = $conn->query($medicines_sql);

                    if ($medicines_result->num_rows > 0) {
                        while ($med_row = $medicines_result->fetch_assoc()) {
                            echo '<div class="form-check">';
                            echo '<input class="form-check-input" type="checkbox" name="medicines[]" value="' . $med_row['med_name'] . '">';
                            echo '<label class="form-check-label">' . $med_row['med_name'] . '</label>';
                            echo '<input type="number" class="form-control mt-1" name="med_quantity[' . $med_row['med_name'] . ']" placeholder="Quantity">';
                            echo '</div>';
                        }
                    } else {
                        echo 'No medicines available.';
                    }
                    ?>
                </div>
                <div class="mb-3">
                    <label for="tests" class="form-label">Tests</label>
                    <?php
                    // Fetch available tests from the database
                    $tests_sql = "SELECT test_id FROM test";
                    $tests_result = $conn->query($tests_sql);

                    if ($tests_result->num_rows > 0) {
                        while ($test_row = $tests_result->fetch_assoc()) {
                            echo '<div class="form-check">';
                            echo '<input class="form-check-input" type="checkbox" name="tests[]" value="' . $test_row['test_id'] . '">';
                            echo '<label class="form-check-label">' . $test_row['test_id'] . '</label>';
                            echo '</div>';
                        }
                    } else {
                        echo 'No tests available.';
                    }
                    ?>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>