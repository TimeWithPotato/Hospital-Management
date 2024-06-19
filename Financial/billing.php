<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include ('../Admin/connect.php');

function getMedCost($conn, $pat_id)
{
    $sql = "SELECT SUM(m.med_price * mp.med_num) AS med_cost
            FROM medicine m
            JOIN med_pres mp ON m.med_name = mp.med_name
            JOIN prescription p ON mp.pres_id = p.pres_id
            WHERE p.pat_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['med_cost'] ?? 0;
}

function getTestCost($conn, $pat_id)
{
    $sql = "SELECT SUM(t.test_cost) AS test_cost
            FROM test t
            JOIN test_pres tp ON t.test_id = tp.test_id
            JOIN prescription p ON tp.pres_id = p.pres_id
            WHERE p.pat_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['test_cost'] ?? 0;
}

function getCabinRent($conn, $pat_id)
{
    $sql = "SELECT c.cabin_rent, DATEDIFF(IFNULL(a.date_of_release, CURDATE()), a.date_of_adm) AS days
            FROM cabin c
            JOIN adm_pat a ON c.pat_id = a.pat_id
            WHERE c.pat_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_rent = 0;
    while ($row = $result->fetch_assoc()) {
        $total_rent += $row['cabin_rent'] * $row['days'];
    }
    return $total_rent;
}

function getFees($conn, $pat_id)
{
    $sql = "SELECT SUM(fees) AS fees FROM prescription WHERE pat_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $pat_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['fees'] ?? 0;
}

function insertBilling($conn, $pat_id, $med_cost, $test_cost, $cabin_rent, $fees)
{
    $sql = "INSERT INTO billing (pat_id, med_cost, test_cost, cabin_rent, fees)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            med_cost = VALUES(med_cost),
            test_cost = VALUES(test_cost),
            cabin_rent = VALUES(cabin_rent),
            fees = VALUES(fees)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdddd", $pat_id, $med_cost, $test_cost, $cabin_rent, $fees);
    $stmt->execute();
}

$calculated = 0;
$billingData = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pat_id'])) {
    $pat_id = $_POST['pat_id'];

    $conn->begin_transaction();
    try {
        // Check if patient exists
        $check_pat_sql = "SELECT * FROM patient WHERE pat_id = ?";
        $stmt = $conn->prepare($check_pat_sql);
        $stmt->bind_param("s", $pat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            throw new Exception("Patient does not exist.");
        }

        $med_cost = getMedCost($conn, $pat_id);
        $test_cost = getTestCost($conn, $pat_id);
        $cabin_rent = getCabinRent($conn, $pat_id);
        $fees = getFees($conn, $pat_id);

        insertBilling($conn, $pat_id, $med_cost, $test_cost, $cabin_rent, $fees);

        $conn->commit();
        $calculated = 1;

        header('location:http://localhost/Hospital-Management/financial/billing.php');
        echo "<script>alert('Billing information inserted successfully for patient ID: $pat_id.')</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Failed to insert billing information: " . $e->getMessage() . "')</script>";
    }

    if ($calculated == 1) {
        $bill_info_sql = "SELECT * FROM billing WHERE pat_id = ?";
        $stmt = $conn->prepare($bill_info_sql);
        $stmt->bind_param("s", $pat_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $billingData[] = $row;
        }
    }
}
if ($calculated == 0) {
    $sql = 'SELECT * FROM billing ORDER BY pat_id ';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $billingData[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Billing Entry</title>
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
    <main class="container" style="margin-top:20vh;">
        <section>
            <h1 class="text-center">Billing</h1>
            <form action="billing.php" method="post">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" placeholder="pat_id" name="pat_id"
                        required>
                    <label for="floatingInput">Patient ID</label>
                </div>
                <button class="btn btn-primary mt-3" type="submit" name="submit">Submit</button>
            </form>
        </section>
        <section>
            <table class="table table-hover table-bordered table-success table-striped mt-3">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Medicine Cost</th>
                        <th>Test Cost</th>
                        <th>Cabin Rent</th>
                        <th>Fees</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($billingData)): ?>
                        <?php foreach ($billingData as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['pat_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['med_cost']); ?></td>
                                <td><?php echo htmlspecialchars($row['test_cost']); ?></td>
                                <td><?php echo htmlspecialchars($row['cabin_rent']); ?></td>
                                <td><?php echo htmlspecialchars($row['fees']); ?></td>
                                <td><?php echo htmlspecialchars($row['tot_cost']); ?></td>
                                <!-- Add more fields as necessary -->
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <p class="text-center fw-bold fs-6">N/A</p>
           
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>