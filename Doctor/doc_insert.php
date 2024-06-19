<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include ('../Admin/connect.php');

    $conn->begin_transaction();

    $success = true;

    $docID = $_POST['doc_id'];
    $fName = $_POST['f_name'];
    $mName = $_POST['m_name'];
    $lName = $_POST['l_name'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $buildNum = $_POST['build_num'];
    $appNum = $_POST['app_num'];
    $sex = $_POST['sex'];
    $dateString = $_POST['date_of_join'];
    $timeStamp = strtotime($dateString);
    $formattedDate = date('Y-m-d H:i:s', $timeStamp);
    $fees = $_POST['fees'];
    $deptId = $_POST['dept_id'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $degrees = explode(",", $_POST['degree_name']);
    $institutes = explode(",", $_POST['institute']);
    $phoneNumbers = explode(",", $_POST['phone_num']);
    $specialties = explode(",", $_POST['specialty']);

    try {
        // Insert into doctor table
        $sql = "INSERT INTO doctor(doc_id,f_name,m_name,l_name,street,city,build_num,app_num,sex,date_of_join,fees,dept_id,email,password)
        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssisssdsss", $docID, $fName, $mName, $lName, $street, $city, $buildNum, $appNum, $sex, $formattedDate, $fees, $deptId, $email, $password);
        $stmt->execute();
        if ($stmt->error) {
            throw new Exception("Error: " . $stmt->error);
        }

        // Insert into doc_degree table
        foreach ($degrees as $index => $degree) {
            $sql = "INSERT INTO doc_degree(doc_id,degree_name,institute) VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $docID, $degree, $institutes[$index]);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Error: " . $stmt->error);
            }
        }

        // Insert into doc_phone_num table
        foreach ($phoneNumbers as $number) {
            $sql = "INSERT INTO doc_phone_num(doc_id,phone_num) VALUES(?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $docID, $number);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Error: " . $stmt->error);
            }
        }

        // Insert into doc_speciality table
        foreach ($specialties as $specialty) {
            $sql = "INSERT INTO doc_speciality(doc_id,specialty) VALUES (?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $docID, $specialty);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Error: " . $stmt->error);
            }
        }

        // Commit transaction if all operations are successful
        $conn->commit();
        echo "<script>alert('Data inserted successfully!');</script>";
    } catch (Exception $e) {
        // Rollback transaction if any error occurs
        $conn->rollback();
        echo "<div class='alert alert-danger error'>
        <span class='close-btn'>&times;</span>
        <p>Error: ". $e->getMessage()."</p>
    </div>";

    }
    // Close connection
    $conn->close();
}
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
                    </div>
                </div>
            </nav>
        </div>
    </header>
    <main style="margin-top:20vh;">
        <section class=" container m-5">
            <h1 class="text-center">Doctor Insert Form</h1>
            <form method="POST">
                <div class="form-floating mb-3">
                    <select class="form-control" id="deptID" name="dept_id" required>
                        <option value="" disabled selected>Department</option>
                        <option value="cardi101">Cardiology</option>
                        <option value="neuro201">Neurology</option>
                        <option value="derma301">Dermatology</option>
                    </select>
                    <label for="deptID">Select Department ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="docId" placeholder="doc145" name="doc_id" required>
                    <label for="docId">Enter Doctor ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="firstName" placeholder="Mohammed" name="f_name"
                        required>
                    <label for="firstName">First Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="middleName" placeholder="Abu" name="m_name">
                    <label for="middleName">Enter Middle Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="lastName" placeholder="Zafar" name="l_name" required>
                    <label for="lastName">Enter Last Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="street" placeholder="4,B-blcok,Bashundhara"
                        name="street" required>
                    <label for="street">Enter road,block,area</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="city" placeholder="Dhaka" name="city" required>
                    <label for="city">Enter City</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="buildingNumber" placeholder="212" name="build_num"
                        required>
                    <label for="buildingNumber">Enter Building Number</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="appartmentNumber" placeholder="F-4" name="app_num"
                        required>
                    <label for="appartmentNumber">Enter Appartment Number</label>
                </div>
                <div class="form-floating mb-3">
                    <select class="form-control" id="gender" name="sex" required>
                        <option value="" disabled required>Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Others">Other</option>
                    </select>
                    <label for="gender">Select Gender </label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="dateOfJoin" placeholder="Join Date" name="date_of_join"
                        required>
                    <label for="dateOfJoin">Enter Date of Join</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="fees" placeholder="Fees" name="fees" required>
                    <label for="fees">Enter Fees</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" placeholder="email id" name="email" required>
                    <label for="email">Enter Email</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="password" placeholder="temporary password"
                        name="password" required>
                    <label for="password">Enter Password</label>
                </div>
                <div class="mb-3">
                    <fieldset>
                        <legend>Degrees and Institutes</legend>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" name="degree_name" id="degree" rows="4" required></textarea>
                            <label for="degree">Degrees (comma separated):</label>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" name="institute" id="institute" rows="4" required></textarea>
                            <label for="institute">Institutes (comma separated, corresponding to degrees):</label>
                        </div>
                    </fieldset>
                </div>
                <div class="mb-3">
                    <fieldset>
                        <legend>Phone Number</legend>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" name="phone_num" id="phoneNumber" rows="4"
                                required></textarea>
                            <label for="phoneNumber">Enter Phone Number (comma separated):</label>
                        </div>
                    </fieldset>
                </div>
                <div class="mb-3">
                    <fieldset>
                        <legend>Specialties</legend>
                        <div class="form-floating mb-3">
                            <textarea class="form-control" name="specialty" id="specialties" rows="4"
                                required></textarea>
                            <label for="specialties">Enter Specialties (comma separated):</label>
                        </div>
                    </fieldset>
                </div>
                <div class="text-center mb-3">
                    <button type="submit" class="btn btn-primary w-75">Submit</button>
                </div>
            </form>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/utilities.js"></script>
</body>

</html>