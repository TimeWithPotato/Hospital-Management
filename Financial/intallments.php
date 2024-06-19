<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include('../Admin/connect.php');
// Check if search input is provided
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}
// Query to retrieve data
$sql = "SELECT 
            i.pay_id, 
            i.ins_id, 
            i.tot_ins, 
            i.ins_count, 
            i.amount_rem, 
            i.next_ins_amount, 
            GROUP_CONCAT(DISTINCT ia.ins_num SEPARATOR ', ') AS ins_num_list, 
            GROUP_CONCAT(ia.ins_amount SEPARATOR ', ') AS ins_amount_list
        FROM 
            installment i
        JOIN 
            ins_amount ia ON i.ins_id = ia.ins_id
        WHERE i.ins_id LIKE '%$searchQuery%' OR
              i.pay_id LIKE '%$searchQuery%'
        GROUP BY 
            i.pay_id, 
            i.ins_id, 
            i.tot_ins, 
            i.ins_count, 
            i.amount_rem, 
            i.next_ins_amount
        ORDER BY pay_id";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installment Details</title>
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
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="http://localhost/Hospital-Management/logout.php">Logout
                                </a>
                            </li>
                        </ul>
                        <form method="post" class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search" name="search"
                                    value="<?php echo htmlspecialchars($searchQuery); ?>">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                    </div>
                </div>
            </nav>
        </div>
    </header>
<main class="container" style="margin-top:20vh;">
    <section>
    <div>
    <h1 class="text-center">Installment Details</h1>
    <table class="table table-hover table-bordered table-success table-striped">
        <thead>
            <tr>
                <th>Pay ID</th>
                <th>Installment ID</th>
                <th>Total Installments</th>
                <th>Installment Count</th>
                <th>Amount Remaining</th>
                <th>Next Installment Amount</th>
                <th>Installment Numbers</th>
                <th>Installment Amounts</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["pay_id"] . "</td>";
                    echo "<td>" . $row["ins_id"] . "</td>";
                    echo "<td>" . $row["tot_ins"] . "</td>";
                    echo "<td>" . $row["ins_count"] . "</td>";
                    echo "<td>" . $row["amount_rem"] . "</td>";
                    echo "<td>" . $row["next_ins_amount"] . "</td>";
                    echo "<td>" . $row["ins_num_list"] . "</td>";
                    echo "<td>" . $row["ins_amount_list"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8'>No data available</td></tr>";
            }
            ?>
        </tbody>
    </tabl>
</div>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
