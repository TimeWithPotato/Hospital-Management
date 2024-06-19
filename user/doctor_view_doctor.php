<?php
include ('../Admin/connect.php');
session_start();
if (!isset($_SESSION['doc_id'])) {
    header('location:Login.php');
}
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
                    <a class="navbar-brand" href="http://localhost/Hospital-Management/user/doctor_homepage.php">DoctorBari</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="http://localhost/Hospital-Management/user/doctor.php">Profile</a>
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
                                                    <input name="doc_id" type="text" class="form-control my-3" id="floatingDocId" placeholder="pat id">
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
                        <th>Sex</th>
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
                               <td>" . $row['sex'] . "</td>
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
    <script src="..js/department.js"></script>
</body>
</html>