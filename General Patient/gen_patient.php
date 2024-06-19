<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include ('../Admin/connect.php');
// Check if search input is provided
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// SQL query to fetch details for patients without admission IDs
$sql = "
WITH med_costs AS (
    SELECT 
        pr.pat_id,
        GROUP_CONCAT(DISTINCT m.med_name SEPARATOR ', ') AS med_names,
        SUM(m.med_price * mp.med_num) AS total_med_cost
    FROM 
        prescription pr
    JOIN med_pres mp ON pr.pres_id = mp.pres_id
    JOIN medicine m ON mp.med_name = m.med_name
    GROUP BY 
        pr.pat_id
),
test_costs AS (
    SELECT 
        pr.pat_id,
        SUM(t.test_cost) AS total_test_cost
    FROM 
        prescription pr
    JOIN test_pres tp ON pr.pres_id = tp.pres_id
    JOIN test t ON tp.test_id = t.test_id
    GROUP BY 
        pr.pat_id
),
fees_costs AS (
    SELECT
        pr.pat_id,
        SUM(pr.fees) AS total_fees_cost
    FROM
        prescription pr
    GROUP BY
        pr.pat_id
)
SELECT 
    p.pat_id,
    b.tot_cost AS total,
    CONCAT(p.f_name, ' ', p.l_name) AS patient_name,
    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age,
    GROUP_CONCAT(DISTINCT ppn.phone_num SEPARATOR '\n') AS phone_num,
    GROUP_CONCAT(DISTINCT pr.pres_id SEPARATOR '\n') AS pr_ids,
    GROUP_CONCAT(DISTINCT pr.date SEPARATOR '\n') AS pr_date,
    GROUP_CONCAT(DISTINCT pr.medication_details SEPARATOR '\n') AS medication_details,
    mc.med_names,
    CONCAT(d.f_name,' ',d.m_name,' ',d.l_name) AS doc_name ,
    mc.total_med_cost AS medicine_bill,
    tc.total_test_cost AS test_bill,
    fc.total_fees_cost AS fees
FROM 
    patient p
LEFT JOIN billing b ON p.pat_id = b.pat_id
LEFT JOIN pat_phone_num ppn ON p.pat_id = ppn.pat_id
LEFT JOIN prescription pr ON p.pat_id = pr.pat_id
LEFT JOIN doctor d ON pr.doc_id = d.doc_id
LEFT JOIN med_costs mc ON p.pat_id = mc.pat_id
LEFT JOIN test_costs tc ON p.pat_id = tc.pat_id
LEFT JOIN fees_costs fc ON p.pat_id = fc.pat_id
LEFT JOIN adm_pat ap ON p.pat_id = ap.pat_id
WHERE ap.adm_id IS NULL AND (    CONCAT(p.f_name, ' ', p.l_name) LIKE '%$searchQuery%' OR
p.pat_id LIKE '%$searchQuery%' OR
pr.pres_id LIKE '%$searchQuery%' OR
pr.date LIKE '%$searchQuery%' OR  
d.doc_id LIKE '%$searchQuery%' OR 
CONCAT(d.f_name,' ',d.m_name,' ',d.l_name) LIKE '%$searchQuery%') 
GROUP BY 
    p.pat_id,
    patient_name,
    age,
    doc_name,
    med_names
ORDER BY 
    p.pat_id;
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$billingData = [];
while ($row = $result->fetch_assoc()) {
    $billingData[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Non-Admitted Patient Billing Information</title>
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
                                    Update
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div class="dropdown-item">
                                            <form action="gen_pat_up_form.php" method="POST">
                                                <div class="form-floating mb-3">
                                                    <input name="pat_id" type="text" class="form-control"
                                                        id="floatingText" placeholder="patient id">
                                                    <label for="floatingText">patient id</label>
                                                    <button type="submit" class="btn btn-primary mt-2">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Delete
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div class="dropdown-item">
                                            <form action="gen_pat_del.php" method="POST">
                                                <div class="form-floating mb-3">
                                                    <input name="pat_id" type="text" class="form-control"
                                                        id="floatingText" placeholder="patient id">
                                                    <label for="floatingText">patient id</label>
                                                    <button type="submit" class="btn btn-primary mt-2">Delete</button>
                                                </div>
                                            </form>
                                        </div>
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
    <main style="margin-top:20vh;">
        <section class="container">
            <div>
                <h2 class="mb-4 text-center">General Patient Information</h2>
                <table class="table table-hover table-bordered table-success table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Phone Number</th>
                            <th>Prescription IDs</th>
                            <th>Prescription Dates</th>
                            <th>Medication Details</th>
                            <th>Medicine Names</th>
                            <th>Doctor Name</th>
                            <th>Medicine Bill</th>
                            <th>Test Bill</th>
                            <th>Fees</th>
                            <th>Total Bill</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($billingData)):  ?>
                            <?php $serial = 0 ?>
                            <?php foreach($billingData as $row) ?>
                            <?php $serial++ ?>
                            <tr>
                            <?php $total = $row['medicine_bill'] + $row['test_bill'] + $row['fees'] ?>
                                <td><?php echo htmlspecialchars($serial); ?></td>
                                <td><?php echo htmlspecialchars($row['pat_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['age']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone_num']); ?></td>
                                <td><?php echo htmlspecialchars($row['pr_ids']); ?></td>
                                <td><?php echo htmlspecialchars($row['pr_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['medication_details']); ?></td>
                                <td><?php echo htmlspecialchars($row['med_names']); ?></td>
                                <td><?php echo htmlspecialchars($row['doc_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['medicine_bill']); ?></td>
                                <td><?php echo htmlspecialchars($row['test_bill']); ?></td>
                                <td><?php echo htmlspecialchars($row['fees']); ?></td>
                                <td><?php echo htmlspecialchars($row['total']); ?></td>
                            </tr>
                        <?php else: ?> 
                            <tr>
                                <td colspan="14"><p class="text-center fw-bold fs-6">N/A</p></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>