<?php
include '../Admin/connect.php';
session_start();
if (!isset($_SESSION['pat_id'])) {
    header('location:Login.php');
}
// echo $_SESSION['pat_id'];
$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}
//Retreiving Doctor 
$get_doc_sql = "SELECT 
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


$doc_num = 0;
$doc_result = mysqli_query($conn, $get_doc_sql);
if ($doc_result) {
    $doc_num = mysqli_num_rows($doc_result);
}
$pat_id = $_SESSION['pat_id'];

// Retrieve Patient Information
$pat_sql = "
SELECT p.pat_id, CONCAT(p.f_name, ' ', p.l_name) AS patient_name,
       p.dob, p.address, p.sex, p.email,
       TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS age,
       a.adm_id, a.date_of_adm, a.date_of_release,
       GROUP_CONCAT(ppn.phone_num SEPARATOR ', ') AS phone_num
FROM patient p
LEFT JOIN adm_pat a ON p.pat_id = a.pat_id
LEFT JOIN pat_phone_num ppn ON p.pat_id = ppn.pat_id
WHERE p.pat_id = ?
GROUP BY 
    pat_id,
    patient_name,
    dob,
    address,
    sex,
    email,
    age,
    adm_id,
    date_of_adm,
    date_of_release

";
$stmt = $conn->prepare($pat_sql);
$stmt->bind_param("s", $pat_id);
$stmt->execute();
$pat_result = $stmt->get_result();

// Check if patient information is found
if ($pat_result->num_rows > 0) {
    $pat_row = $pat_result->fetch_assoc(); // Fetch single row
} else {
    // No patient information found, handle it as needed
    $row = null; // Set $row to null to avoid errors later
}

// Close the database connection
$stmt->close();
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
                    <a class="navbar-brand" href="http://localhost/Hospital-Management/user/patient_homepage.php">DoctorBari</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="http://localhost/Hospital-Management/user/patient.php">Profile</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Change Password
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div class="dropdown-item" style="width: 25em;">
                                            <form action="pass_change.php" method="POST">
                                                <div class="form-floating mb-3">
                                                    <input name="pat_id" type="text" class="form-control my-3" id="floatingDocId" placeholder="pat id">
                                                    <label for="floatingDocId">Patient ID</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input name="pat_email" type="text" class="form-control" id="floatingEmail" placeholder="email">
                                                    <label for="floatingEmail">Email</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input name="pat_password" type="text" class="form-control" id="floatingPassword" placeholder="new password">
                                                    <label for="floatingPassword">New Password</label>
                                                </div>
                                                <button type="submit" class="btn btn-primary mt-2">Submit</button>
                                            </form>
                                        </div>

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
        <section class="container">
            <div>
                <h2 class="mb-4 text-center">General Patient Information</h2>
                <table class="table table-hover table-bordered table-success table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Date of Birth</th>
                            <th>Address</th>
                            <th>Sex</th>
                            <th>Email</th>
                            <th>Age</th>
                            <th>Admission ID</th>
                            <th>Date of Admission</th>
                            <th>Date of Release</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($pat_result->num_rows > 0) :  ?>
                            <?php $serial = 1 ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($serial); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['pat_id']); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['dob']); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['address']); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['sex']); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['email']); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['age']); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['adm_id']); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['date_of_adm']); ?></td>
                                    <td><?php echo htmlspecialchars($pat_row['date_of_release']); ?></td>
                                </tr>

                        <?php else : ?>
                            <tr>
                                <td colspan="14">
                                    <p class="text-center fw-bold fs-6">N/A</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="container">
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
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($doc_num > 0) {
                        $serial = 1;
                        while ($doc_row = mysqli_fetch_assoc($doc_result)) {
                            echo "
                            <tr>
                              <td>" . $serial . "</td>
                              <td>" . $doc_row['dept_name'] . "</td>
                              <td>" . $doc_row['doc_id'] . "</td>
                              <td>" . $doc_row['Name'] . "</td>
                              <td>" . $doc_row['Specialities'] . "</td>
                              <td>" . $doc_row['Degrees'] . "</td>
                              <td>" . $doc_row['fees'] . "</td>
                              <td>" . $doc_row['email'] . "</td>
                              <td>" . $doc_row['Phone_Numbers'] . "</td>
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
</body>
</html>