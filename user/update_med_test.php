<?php
include ('../Admin/connect.php');
session_start();
if (!isset($_SESSION['doc_id'])) {
    header('location:Login.php');
}
$doc_id = $_SESSION['doc_id'];

include '../Admin/connect.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $pat_id = '';
    $doc_id = '';
    $next_fees = $_POST['fees'];
    $pres_id = $_POST['pres_id'];
    $medicines = isset($_POST['medicines']) ? $_POST['medicines'] : [];
    $med_quantity = isset($_POST['med_quantity']) ? $_POST['med_quantity'] : [];
    $tests = isset($_POST['tests']) ? $_POST['tests'] : [];

    $conn->begin_transaction();
    try{

        //Check doc_id, pat_id
        $sql = "SELECT pat_id FROM prescription WHERE pres_id = ? AND doc_id =?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $pres_id,$doc_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows <= 0){
            throw new Exception('Patient does not exist');
        }
        $row = $result->fetch_assoc();
        $pat_id = $row['pat_id'];

        //store prev prescription medication details
        $prev_medication_details = '';
        $sql = "SELECT medication_details FROM prescription WHERE pres_id = ? AND pat_id = ? AND doc_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $pres_id, $pat_id, $doc_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            $rows = $result->fetch_assoc();
            $row = $rows['medication_details'];
            $prev_medication_details .= $row;
        }
        else{
            throw new Exception('Prescription ID does not exist');
        }

        //Update the medication details of the prescription
        $next_medication_details= $_POST['medication_details'];
        $updated_medication_details = $prev_medication_details . ',' . $next_medication_details;

        $sql = 'UPDATE prescription SET medication_details = ? WHERE pres_id = ? AND doc_id = ? AND pat_id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $updated_medication_details, $pres_id, $doc_id, $pat_id);
        $stmt->execute();

        //Update the fees
        $prev_fees = 0;
        $sql = "SELECT fees FROM prescription WHERE pres_id = ? AND doc_id = ? AND pat_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $pres_id, $doc_id, $pat_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $row = $result->fetch_assoc();
            $prev_fees = $row['fees'];
        }
        else{
            throw new Exception('Error : No previous fees or pres id does not exist');
        }

        $updated_fees = $prev_fees + $next_fees;
        $sql = 'UPDATE prescription SET fees = ? WHERE pres_id = ? AND doc_id = ? AND pat_id = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dsss", $updated_fees, $pres_id, $doc_id, $pat_id);
        $stmt->execute();

         // update (insert into) medicine and test
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

        $conn->commit();
        echo "<script>
        alert('Medicines and tests updated successfully.');
        window.location.href = 'http://localhost/Hospital-Management/Medicine-test/update_med_test.php';
        </script>";

    }catch(Exception $e){
        $conn->rollback();
        echo "<div class='alert alert-danger error'>
        <span class='close-btn'>&times;</span>
        <p>Error: " . $e->getMessage() . "</p>
      </div>";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<header>
        <div class="w-75 mx-auto my-3">
            <nav class="navbar fixed-top navbar-expand-lg nav-body">
                <div class="container-fluid">
                    <a class="navbar-brand" href="http://localhost/Hospital-Management/user/doctor_homepage.php">DoctorBari</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Change Password
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div class="dropdown-item">
                                            <form action="pass_change.php" method="POST">
                                                <div class="form-floating mb-3">
                                                    <input name="doc_id" type="text" class="form-control"
                                                        id="floatingText" placeholder="doctor id">
                                                    <label for="floatingText">Doctor ID</label>
                                                    <input name="email" type="text" class="form-control"
                                                        id="floatingText" placeholder="email">
                                                    <label for="floatingText">Email</label>
                                                    <input name="password" type="text" class="form-control"
                                                        id="floatingText" placeholder="new password">
                                                    <label for="floatingText">New Password</label>
                                                    <button type="submit" class="btn btn-primary mt-2">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="http://localhost/Hospital-Management/user/doctor.php">Your patients</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Prescription
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-item">
                                        <a class="nav-link" aria-current="page" href="http://localhost/Hospital-Management/user/ins_med_test.php">New Prescription</a>
                                    </li>
                                    <li class="dropdown-item">
                                        <a class="nav-link" aria-current="page" href="http://localhost/Hospital-Management/user/update_med_test.php">Update Prescription</a>
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
    <main class="container">
    <section>
        <h1 class="text-center" style="margin-top:5%;">Update Prescription</h1>
            <form action="update_med_test.php" class="form-floating mb-3" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" name="pres_id" required>
                    <label for="floatingInput">Prescription ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="floatingInput" name="fees" required>
                    <label for="floatingInput">Fees</label>
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
    <script src="./js/utilities.js"></script>
</body>
</html>