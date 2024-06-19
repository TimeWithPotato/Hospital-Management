<?php
include '../Admin/connect.php';
session_start();
if (!isset($_SESSION['doc_id'])) {
    header('location:Login.php');
}
$searchQuery = '';
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}
$doc_id = $_SESSION['doc_id'];

$sql = "
SELECT p.pat_id,
       CONCAT(p.f_name, ' ', p.l_name) AS 'patient_name',
       TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) AS 'age',
       GROUP_CONCAT(DISTINCT tp.test_id) AS 'test_id',
       GROUP_CONCAT(DISTINCT mp.med_name) AS 'med_name',
       GROUP_CONCAT(DISTINCT ppn.phone_num) AS 'phone_num',
       GROUP_CONCAT(DISTINCT pr.pres_id) AS 'pr_id',
       GROUP_CONCAT(DISTINCT pr.date) AS 'pr_date',
       GROUP_CONCAT(DISTINCT pr.medication_details) AS 'medication_details',
       CONCAT(d.f_name, ' ', d.m_name, ' ', d.l_name) AS 'doc_name'
FROM patient p
JOIN prescription pr ON p.pat_id = pr.pat_id
JOIN pat_phone_num ppn ON p.pat_id = ppn.pat_id
JOIN med_pres mp ON pr.pres_id = mp.pres_id
JOIN test_pres tp ON pr.pres_id = tp.pres_id
JOIN doctor d ON pr.doc_id = d.doc_id
WHERE pr.doc_id = ? AND 
      (p.pat_id LIKE ? OR 
       pr.pres_id LIKE ? OR 
       CONCAT(p.f_name, ' ', p.l_name) LIKE ? OR 
       mp.med_name LIKE ? OR 
       tp.test_id LIKE ?)
GROUP BY p.pat_id, p.f_name, p.l_name, p.dob, d.f_name, d.m_name, d.l_name
ORDER BY p.pat_id;
";

$stmt = $conn->prepare($sql);
$searchParam = "%$searchQuery%";
$stmt->bind_param("ssssss", $doc_id, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);
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
    <title>DoctorBari</title>
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
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Change Password
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <div class="dropdown-item" style="width: 25em;">
                                            <form action="pass_change.php" method="POST">
                                                <div class="form-floating mb-3">
                                                    <input name="doc_id" type="text" class="form-control my-3" id="floatingDocId" placeholder="doctor id">
                                                    <label for="floatingDocId">Doctor ID</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input name="email" type="text" class="form-control" id="floatingEmail" placeholder="email">
                                                    <label for="floatingEmail">Email</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input name="password" type="text" class="form-control" id="floatingPassword" placeholder="new password">
                                                    <label for="floatingPassword">New Password</label>
                                                </div>
                                                <button type="submit" class="btn btn-primary mt-2">Submit</button>
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
                        <form method="POST" class="mb-4">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
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
                            <th>Test ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($billingData)) :  ?>
                            <?php $serial = 0 ?>
                            <?php foreach ($billingData as $row) : ?>
                                <?php $serial++ ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($serial); ?></td>
                                    <td><?php echo htmlspecialchars($row['pat_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['age']); ?></td>
                                    <td><?php echo htmlspecialchars($row['phone_num']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pr_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pr_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['medication_details']); ?></td>
                                    <td><?php echo htmlspecialchars($row['med_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['doc_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['test_id']); ?></td>
                                </tr>
                            <?php endforeach; ?>

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
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/utilities.js"></script>
</body>

</html>