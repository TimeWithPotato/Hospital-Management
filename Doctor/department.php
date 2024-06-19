<?php
include ('../Admin/connect.php');

$num = 0;
$result = null;
$dept_name = '';
if (isset($_POST['dept_id'])) {
    $dept_id = $_POST['dept_id'];
    $sql = "SELECT de.dept_name,
    d.doc_id,
    d.f_name,
    d.m_name,
    d.l_name,
    CONCAT(d.app_num, ', ', d.build_num, ', ', d.street, ', ', d.city) AS address,
    d.sex,
    d.date_of_join,
    GROUP_CONCAT(DISTINCT CONCAT(dd.degree_name, ' (', dd.institute, ')') SEPARATOR '\n') AS degrees,
    GROUP_CONCAT(DISTINCT ds.specialty SEPARATOR'\n') AS specialities,
    d.fees,
    d.email,
    GROUP_CONCAT(DISTINCT dpn.phone_num SEPARATOR '\n') AS phone_nums
FROM department de
JOIN doctor d ON de.dept_id = d.dept_id
JOIN doc_degree dd ON d.doc_id = dd.doc_id
JOIN doc_speciality ds ON d.doc_id = ds.doc_id
JOIN doc_phone_num dpn ON d.doc_id = dpn.doc_id
WHERE de.dept_id = ?
GROUP BY de.dept_name, d.doc_id, d.f_name, d.m_name, d.l_name, address, d.sex, d.date_of_join, d.fees;";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $dept_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result) {
                $num = $result->num_rows;
                if($num>0){
                    $row = $result->fetch_assoc();
                    $dept_name = $row['dept_name'];
                    $result->data_seek(0);
                }
                else{
                    echo "<script>alert('Error getting result:".$stmt->error." ')</script>";
                }
            } else {
                echo "<script>alert('Error getting result:".$stmt->error." ')</script>";
            }
        } else {
            echo "<script>alert('Error executing query: ".$stmt->error."')</script>";
        }
    } else {
        echo "<script>alert('Error preparing statement: ".$stmt->error." ". $conn->error."')</script>";
    }
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
    <main style="margin-top:20vh;">
        <section>

        <h1 class="text-center">Doctors List of <?php echo htmlspecialchars($row['dept_name']) ?></h1>
            <table class="table table-hover table-bordered table-success table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Doc_id</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Address</th>
                        <th>Sex</th>
                        <th>Date of Join</th>
                        <th>Degree</th>
                        <th>Speciality</th>
                        <th>Fees</th>
                        <th>Email</th>
                        <th>Phone Number</th>
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
                               <td>" . $row['doc_id'] . "</td>
                               <td>" . $row['f_name'] . "</td>
                               <td>" . $row['m_name'] . "</td>
                               <td>" . $row['l_name'] . "</td>
                               <td>" . $row['address'] . "</td>
                               <td>" . $row['sex'] . "</td>
                               <td>" . $row['date_of_join'] . "</td>
                               <td>" . $row['degrees'] . "</td>
                               <td>" . $row['specialities'] . "</td>
                               <td>" . $row['fees'] . "</td>
                               <td>" . $row['email'] . "</td>
                               <td>" . $row['phone_nums'] . "</td>
                            </tr>";
                            $serial++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/department.js"></script>
</body>
</html>