<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include ('../Admin/connect.php');
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}
// Default SQL query without search filter
$sql = "SELECT 
    de.dept_name, d.doc_id,
    CONCAT(d.f_name,' ',d.m_name,' ',d.l_name) AS Name, 
    GROUP_CONCAT(DISTINCT ds.specialty SEPARATOR '\n') AS Specialities, 
    GROUP_CONCAT(DISTINCT CONCAT(dd.degree_name,' (', dd.institute, ')') SEPARATOR '\n') AS Degrees, 
    d.fees,
    d.email,
    GROUP_CONCAT(DISTINCT dpn.phone_num SEPARATOR '\n') AS Phone_Numbers
    FROM 
    doctor d
    JOIN 
    department de ON d.dept_id = de.dept_id
    JOIN 
    doc_speciality ds ON d.doc_id = ds.doc_id
    JOIN 
    doc_degree dd ON d.doc_id = dd.doc_id
    JOIN 
    doc_phone_num dpn ON d.doc_id = dpn.doc_id
    WHERE 
    d.doc_id = '{$searchQuery}' OR
    de.dept_name LIKE '%{$searchQuery}%' OR
    CONCAT(d.f_name,' ',d.m_name,' ',d.l_name) LIKE '%{$searchQuery}%' OR
    ds.specialty LIKE '%{$searchQuery}%' OR
    dd.degree_name LIKE '%{$searchQuery}%' OR
    dpn.phone_num LIKE '%{$searchQuery}%'
    GROUP BY NAME
    ORDER BY de.dept_name";


$num = 0;
$result = mysqli_query($conn, $sql);
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
                        <form method="post" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search" name="search"
                                value="<?php echo htmlspecialchars($searchQuery); ?>">
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </form>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main class="container" style="margin-top:10rem">
        <section>
            <!-- ALL DOCTORS -->
            <h1 class="text-center">Doctor List</h1>
            <table class="table table-hover table-bordered table-success table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Department Name</th>
                        <th>Doctor ID</th>
                        <th>Doctor Name</th>
                        <th>Speciality</th>
                        <th>Degrees</th>
                        <th>Fees</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th> </th>
                        <th> </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($num > 0) {
                        $serial = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "
                            <tr>
                              <td>" . $serial . "</td>
                              <td>" . $row['dept_name'] . "</td>
                              <td>" . $row['doc_id'] . "</td>
                              <td>" . $row['Name'] . "</td>
                              <td>" . $row['Specialities'] . "</td>
                              <td>" . $row['Degrees'] . "</td>
                              <td>" . $row['fees'] . "</td>
                              <td>" . $row['email'] . "</td>
                              <td>" . $row['Phone_Numbers'] . "</td>
                              <td>
                                <form action='doc_update.php' method='POST'>
                                <input type='hidden' name='doc_id' value='" . $row['doc_id'] . "'>
                                <button type='submit' name='update' class='btn btn-warning btn-sm text-white'>Update</button>
                                </form>
                              </td>
                              <td>
                                <form action='doc_update_delete.php' method='POST'>
                                <input type='hidden' name='doc_id' value='" . $row['doc_id'] . "'>
                                <button type='submit' name='delete' class='btn btn-danger btn-sm'>Delete</button>
                                </form>
                              </td>
                            </tr>
                            ";
                            $serial++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/utilities.js"></script>
    <script src="../js/department.js"></script>
</body>

</html>