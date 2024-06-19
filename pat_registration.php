<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include ('./Admin/connect.php');

//Retrieve the pat_id
$pat_id = '';
$sql = "SELECT pat_id FROM patient ORDER BY pat_id DESC limit 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_id = $row['pat_id'];
    $numeric_part = intval(substr($last_id, 3)) + 1;
    $pat_id = 'pat' . $numeric_part;
} else {
    $pat_id = 'pat1';
}

//Retrieve adm_id
$adm_id = '';
$sql = "SELECT adm_id FROM adm_pat ORDER BY adm_id DESC limit 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_id = $row['adm_id'];
    $numeric_part = intval(substr($last_id, 3)) + 1;
    $adm_id = 'adm' . $numeric_part;
} else {
    $adm_id = 'adm001';
}

//Retrieve cabin_id
$cabin_id = '';
$sql = "SELECT cabin_id FROM cabin ORDER BY cabin_id DESC limit 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $last_id = $row['cabin_id'];
    $numeric_part = intval(substr($last_id, 3)) + 1;
    $cabin_id = 'cabin' . $numeric_part;
} else {
    $cabin_id = 'cabin001';
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn->begin_transaction();
    $email = $_POST['email'];

    if (empty($email)) {
        $email = '';
    }
    $pat_id = $_POST['pat_id'];
    $adm_id = $_POST['adm_id'] ?? '';
    $ward_id = $_POST['ward_id'] ?? '';
    $cabin_rent = $_POST['cabin_rent'] ?? '';
    $cabin_id = $_POST['cabin_id'] ?? '';
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $patientPhoneNumberArray = explode(",", $_POST['phone_num']);
    $address = $_POST['address'];
    $dob_str = $_POST['dob'];
    $dob_obj = DateTime::createFromFormat('Y-m-d', $dob_str);
    $dob = $dob_obj->format('Y-m-d');
    $sex = $_POST['sex'];

    try {
        $pat_reg_sql = "INSERT INTO patient(pat_id,f_name,l_name,address,dob,sex,email) VALUES (?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($pat_reg_sql);
        $stmt->bind_param("sssssss", $pat_id, $f_name, $l_name, $address, $dob, $sex, $email);
        $stmt->execute();
        if ($stmt->error) {
            throw new Exception("Error: " . $stmt->error);
        }

        foreach ($patientPhoneNumberArray as $patientPhoneNumber) {
            if (strlen($patientPhoneNumber != 14)) {
                if ($stmt->error) {
                    throw new Exception("Error: phone number length must be 14");
                }
            }
            $patient_phone_number_sql = "INSERT INTO pat_phone_num(pat_id,phone_num) VALUES(?,?)";
            $stmt = $conn->prepare($patient_phone_number_sql);
            $stmt->bind_param("ss", $pat_id, $patientPhoneNumber);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Error:" . $stmt->error);
            }
        }

        if (!empty($adm_id)) {
            $date_str = $_POST['date_of_adm'];
            $date_obj = DateTime::createFromFormat('Y-m-d', $date_str);
            $date_of_adm = $date_obj->format('Y-m-d');
            $adm_reg_sql = "INSERT INTO adm_pat(adm_id,date_of_adm,pat_id) VALUES(?,?,?)";
            $stmt = $conn->prepare($adm_reg_sql);
            $stmt->bind_param("sss", $adm_id, $date_of_adm, $pat_id);
            $stmt->execute();

            $cabin_insert_sql = "INSERT INTO cabin(cabin_id,ward_id,pat_id,cabin_rent) VALUES (?,?,?,?)";
            $stmt = $conn->prepare($cabin_insert_sql);
            $stmt->bind_param("ssss", $cabin_id, $ward_id, $pat_id, $cabin_rent);
            $stmt->execute();
            if ($stmt->error) {
                throw new Exception("Error:" . $stmt->error);
            }

        }

        $conn->commit();
        echo "<script>alert('Data inserted successfully!');</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger error'>
                <span class='close-btn'>&times;</span>
                <p>Error: " . $e->getMessage() . "</p>
              </div>";
    }

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
    <link rel="stylesheet" href="style.css">
    <style>
        .d-none {
            display: none !important;
        }
    </style>
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
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="http://localhost/Hospital-Management/Medicine-test/ins_med_test.php">Prescription</a>
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
    <main>
        <section class="container" style="margin-top:10vh;">
            <form action="pat_registration.php" method="post" class="form-floating mb-3">
                <div class="form-floating mb-3">
                    <input name="email" type="email" class="form-control" id="floatingInput"
                        placeholder="name@example.com">
                    <label for="floatingText">Email address</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="pat_id" type="text" class="form-control text-danger fw-bold fs-5" id="floatingText"
                        value="<?php echo htmlspecialchars($pat_id); ?>" placeholder="Patient ID" required>
                    <label for="floatingText">Patient ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="checkbox" id="toggle-adm-id" class="form-check-input mb-3 ">
                    <label for="toggle-adm-id" class="form-check-label ms-2" style="margin-left:10px; padding-top:1px"><span class="text-danger fw-bold">Include Admission ID</span> </label>
                </div>
                <div class="form-floating mb-3" id="adm-id-container">
                    <input name="adm_id" type="text" class="form-control text-danger fw-bold fs-5" id="adm_id"
                        value="<?php echo htmlspecialchars($adm_id); ?>" placeholder="Admission ID" disabled>
                    <label for="adm_id">Admission ID</label>
                </div>
                <div class="form-floating mb-3" id="ward-id-container">
                    <select class="form-control" id="ward_id" name="ward_id" disabled>
                        <option value="" disabled selected>Ward</option>
                        <option value="ward101">Cardiology</option>
                        <option value="ward201">Neurology</option>
                        <option value="ward301">Dermatology</option>
                    </select>
                </div>
                <div class="form-floating mb-3" id="cabin-rent-container">
                    <select class="form-control" id="cabin_rent" name="cabin_rent" disabled>
                        <option value="" disabled selected>Cabin Type</option>
                        <option value="1000">Type A</option>
                        <option value="2000">Type B</option>
                        <option value="4000">Type C</option>
                    </select>
                </div>

                <div class="form-floating mb-3" id="cabin-id-container">
                    <input name="cabin_id" type="text" class="form-control text-danger fw-bold fs-5" id="cabin_id"  value="<?php echo htmlspecialchars($cabin_id); ?>" placeholder="Cabin ID" disabled>
                    <label for="floatingText">Cabin ID</label>
                </div>
                <div class="form-floating mb-3" id="date-of-adm-container">
                    <input name="date_of_adm" type="date" class="form-control" id="date_of_adm"
                        placeholder="Date of Admission" disabled>
                    <label for="date_of_adm">Date of Admission</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="f_name" type="text" class="form-control" id="floatingText" placeholder="First Name"
                        required>
                    <label for="floatingText">First Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="l_name" type="text" class="form-control" id="floatingText" placeholder="Last Name"
                        required>
                    <label for="floatingText">Last Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="phone_num" type="text" class="form-control" id="phoneNumber" placeholder="Phone Number"
                        required>
                    <label for="floatingText">Phone Number(with +88)</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="address" type="text" class="form-control" id="floatingText" placeholder="Address"
                        required>
                    <label for="floatingInput">Address</label>
                </div>
                <div class="form-floating mb-3">
                    <input name="dob" type="date" class="form-control" id="dob" placeholder="Date of Birth" required>
                    <label for="dob">DOB</label>
                </div>
                <div class="form-floating mb-3">
                    <select class="form-select" name="sex" required>
                        <option selected disabled>Select your gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Others">Other</option>
                    </select>
                </div>
                <button id="btn-submit" type="submit" class="btn btn-primary mt-3" name="submit">Submit</button>
            </form>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/utilities.js"></script>
</body>

</html>