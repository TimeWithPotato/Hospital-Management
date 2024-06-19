<?php
include ('../Admin/connect.php');
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
// Check if search input is provided
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// SQL query to fetch details for admitted patients based on the search input
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
    ap.adm_id,
    ap.date_of_adm,
    ap.date_of_release,
    CONCAT(p.f_name, ' ', p.l_name) AS patient_name,
    TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age,
    GROUP_CONCAT(DISTINCT ppn.phone_num SEPARATOR '\n') AS phone_num,
    GROUP_CONCAT(DISTINCT pr.pres_id SEPARATOR '\n') AS pr_ids,
    GROUP_CONCAT(DISTINCT pr.date SEPARATOR '\n') AS pr_date,
    GROUP_CONCAT(DISTINCT pr.medication_details SEPARATOR '\n') AS medication_details,
    mc.med_names,
    CONCAT(d.f_name, ' ', d.m_name, ' ', d.l_name) AS doc_name,
    w.ward_id,
    c.cabin_id,
    mc.total_med_cost AS medicine_bill,
    tc.total_test_cost AS test_bill,
    fc.total_fees_cost AS fees,
    
    /* Calculate cabin rent up to today's date or date of release */
    CASE 
        WHEN ap.date_of_release='0000-00-00' THEN 
            GREATEST(0, DATEDIFF(CURDATE(), ap.date_of_adm)) * c.cabin_rent
        ELSE 
            GREATEST(0, DATEDIFF(ap.date_of_release, ap.date_of_adm)) * c.cabin_rent
    END AS cabin_rent,
    
    /* Calculate total bill including fees */
    (mc.total_med_cost + 
     tc.total_test_cost + 
     fc.total_fees_cost +
     CASE 
        WHEN ap.date_of_release ='0000-00-00' THEN 
            GREATEST(0, DATEDIFF(CURDATE(), ap.date_of_adm) ) * c.cabin_rent
        ELSE 
            GREATEST(0, DATEDIFF(ap.date_of_release, ap.date_of_adm)) * c.cabin_rent
     END) AS total_bill

FROM 
    patient p
JOIN adm_pat ap ON p.pat_id = ap.pat_id
JOIN cabin c ON p.pat_id = c.pat_id
LEFT JOIN ward w ON c.ward_id = w.ward_id
LEFT JOIN pat_phone_num ppn ON p.pat_id = ppn.pat_id
LEFT JOIN prescription pr ON p.pat_id = pr.pat_id
LEFT JOIN doctor d ON pr.doc_id = d.doc_id
LEFT JOIN med_costs mc ON p.pat_id = mc.pat_id
LEFT JOIN test_costs tc ON p.pat_id = tc.pat_id
LEFT JOIN fees_costs fc ON p.pat_id = fc.pat_id
WHERE 
    CONCAT(p.f_name, ' ', p.l_name) LIKE '%$searchQuery%' OR
    p.pat_id LIKE '%$searchQuery%' OR
    ap.adm_id LIKE '%$searchQuery%' OR
    pr.pres_id LIKE '%$searchQuery%' OR
    pr.date LIKE '%$searchQuery%' OR 
    c.ward_id LIKE '%$searchQuery%' OR 
    c.cabin_id LIKE '%$searchQuery%' OR 
    ap.date_of_adm LIKE '%$searchQuery%' OR 
    ap.date_of_release LIKE '%$searchQuery%' OR 
    d.doc_id LIKE '%$searchQuery%' OR 
    CONCAT(d.f_name,' ',d.m_name,' ',d.l_name) LIKE '%$searchQuery%' 
GROUP BY 
    p.pat_id,
    ap.adm_id,
    ap.date_of_adm,
    ap.date_of_release,
    patient_name,
    age,
    c.cabin_id,
    doc_name,
    w.ward_id,
    med_names,
    cabin_rent
ORDER BY 
    p.pat_id;
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$num = 0;
if ($result) {
    $num = mysqli_num_rows($result);
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admitted Patient Billing Information</title>
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
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="http://localhost/Hospital-Management/homepage.php">Department</a>
                            </li>
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
                                            <form action="adm_pat_up_form.php" method="POST">
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
                                            <form action="adm_pat_del.php" method="POST">
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
        <section>
            <div>
                <h2 class="mb-4 text-center">Admitted Patient Information</h2>
                <table class="table table-hover table-bordered table-success table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient ID</th>
                            <th>Adm ID</th>
                            <th>Date of Adm</th>
                            <th>Date of Release</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>Phone</th>
                            <th>Pres IDs</th>
                            <th>Pres Dates</th>
                            <th>Med Details</th>
                            <th>Med Names</th>
                            <th>D. Name</th>
                            <th>Ward ID</th>
                            <th>Cabin ID</th>
                            <th>Med Bill</th>
                            <th>Test Bill</th>
                            <th>Fees</th>
                            <th>Cabin Rent</th>
                            <th>Total Bill</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($num > 0) {
                            $serial = 1;
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $serial . "</td>";
                                echo "<td>" . $row['pat_id'] . "</td>";
                                echo "<td>" . $row['adm_id'] . "</td>";
                                echo "<td>" . $row['date_of_adm'] . "</td>";
                                echo "<td>" . ($row['date_of_release'] == '0000-00-00' ? 'N/A' : $row['date_of_release']) . "</td>";
                                echo "<td>" . $row['patient_name'] . "</td>";
                                echo "<td>" . $row['age'] . "</td>";
                                echo "<td>" . nl2br($row['phone_num']) . "</td>";
                                echo "<td>" . nl2br($row['pr_ids']) . "</td>";
                                echo "<td>" . nl2br($row['pr_date']) . "</td>";
                                echo "<td>" . nl2br($row['medication_details']) . "</td>";
                                echo "<td>" . nl2br($row['med_names']) . "</td>";
                                echo "<td>" . $row['doc_name'] . "</td>";
                                echo "<td>" . $row['ward_id'] . "</td>";
                                echo "<td>" . $row['cabin_id'] . "</td>";
                                echo "<td>" . $row['medicine_bill'] . "</td>";
                                echo "<td>" . $row['test_bill'] . "</td>";
                                echo "<td>" . $row['fees'] . "</td>";
                                echo "<td>" . $row['cabin_rent'] . "</td>";
                                echo "<td>" . $row['total_bill'] . "</td>";
                                echo "</tr>";
                                $serial++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>