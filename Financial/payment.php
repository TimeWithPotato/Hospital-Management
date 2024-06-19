<?php
include ('../Admin/connect.php');
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
$search_sql = 'SELECT * FROM payment ';
$stmt = $conn->prepare($search_sql);
$stmt->execute();

$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $billingData[] = $row;
}

//Retrieve the payment id
$pay_id = '';
$sql = 'SELECT pay_id FROM payment ORDER BY pay_id DESC LIMIT 1';
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0){
    $row_pay_id = $result->fetch_assoc();
    $prev_pay_id = $row_pay_id['pay_id'];
    $numeric_part = intval(substr($prev_pay_id, 3)) + 1;
    $pay_id = 'pay' . $numeric_part;

}
else{
    $pay_id = 'pay1';
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoctorBari</title>
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
                                            href="http://localhost/Hospital-Management/Financial/pay_installment.php">Pay
                                            Installment</a>
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
                                            href="http://localhost/Hospital-Management/Medicine-test/ins_med_test.php">New
                                            Prescription</a>
                                    </li>
                                    <li class="dropdown-item">
                                        <a class="nav-link" aria-current="page"
                                            href="http://localhost/Hospital-Management/Medicine-test/update_med_test.php">Update
                                            Prescription</a>
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
    <main style="margin-top:10vh;">
        <section class="container">
            <h1 class="text-center">Payment Form</h1>
            <form action="payment_process.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" placeholder="pat_id" name="pat_id">
                    <label for="floatingInput">Patient ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control text-danger fw-semibold fs-5" id="floatingInput" placeholder="pay_id" name="pay_id" value="<?php echo htmlspecialchars($pay_id) ?>">
                    <label for="floatingInput">Payment ID</label>
                </div>
                <div class="form-floating mb-3">
                    <select class="form-select" aria-label="Default select example" name="pay_option"
                        id="pay_option_select">
                        <option selected disabled>Open this select menu</option>
                        <option value="clear">Pay at Once</option>
                        <option value="installment">Pay in Installment</option>
                    </select>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="floatingInput" placeholder="amount" name="amount">
                    <label for="floatingInput">Amount</label>
                </div>
                <div id="installment_options" class="mt-3" style="display: none;">
                    <label for="num_installments" class="form-label">Number of Installments</label>
                    <input type="number" class="form-control" id="num_installments" name="num_installments" min="5"
                        max="10">
                    <label for="installment_id" class="form-label">Installment ID</label>
                    <input type="text" class="form-control" id="installment_id" name="installment_id">
                </div>
                <button class="btn btn-primary mt-3" type="submit" name="submit">Submit</button>
            </form>
        </section>
        <section class="container">
            <h1 class="text-center">Payment Details</h1>
            <table class="table table-hover table-bordered table-success table-striped mt-5">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Payment ID</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($billingData)): ?>
                        <?php foreach ($billingData as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['pat_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['pay_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['pay_status']); ?></td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <p class="text-center fw-bold fs-6">N/A</p>

                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('pay_option_select').addEventListener('change', function () {
                var installmentOptions = document.getElementById('installment_options');
                if (this.value === 'installment') {
                    installmentOptions.style.display = 'block';
                } else {
                    installmentOptions.style.display = 'none';
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/utilities.js"></script>
</body>

</html>