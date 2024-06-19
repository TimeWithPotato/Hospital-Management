<?php
include ('../Admin/connect.php');
session_start();
if (!isset($_SESSION['doc_id'])) {
    header('location:Login.php');
}
include('../Admin/connect.php');

// Check if the doc_id is set in the POST data
if (isset($_POST['doc_id'])) {
    $doc_id = $_POST['doc_id']; // Corrected variable name from 'doc_id'

    // Construct the SQL query using prepared statements to prevent SQL injection
    $sql = "SELECT 
        d.fees,
        GROUP_CONCAT(DISTINCT ds.specialty SEPARATOR ',') AS Specialities, 
        GROUP_CONCAT(DISTINCT dd.degree_name SEPARATOR ',') AS Degrees, 
        GROUP_CONCAT(DISTINCT dd.institute SEPARATOR ',') AS Institutes, 
        GROUP_CONCAT(DISTINCT dpn.phone_num SEPARATOR ',') AS Phone_Numbers
        FROM doctor d
        JOIN doc_speciality ds ON d.doc_id = ds.doc_id
        JOIN doc_degree dd ON d.doc_id = dd.doc_id
        JOIN doc_phone_num dpn ON d.doc_id = dpn.doc_id
        WHERE d.doc_id = ?"; // Using parameterized query

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    // Bind the parameter
    $stmt->bind_param("s", $doc_id);
    // Execute the statement
    $stmt->execute();
    // Get the result
    $result = $stmt->get_result();

    // Check if the result contains any rows
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch the row
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Update Doctor Info</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
            <link rel="stylesheet" href="../style.css">
        </head>

        <body>
        <header>
        <div class="w-75 mx-auto my-3">
            <nav class="navbar fixed-top navbar-expand-lg nav-body">
                <div class="container-fluid">
                    <a class="navbar-brand" href="http://localhost/Hospital-Management/homepage.php">DoctorBari</a>
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
            <main class="container" style="margin-top:10rem">
                <section>
                    <h1 class="text-center">Doctor Update Form</h1>
                    <form action="doc_update_delete.php" method="POST">
                        <div class="mb-3">
                            <input type="hidden" class="form-contorl" name="doc_id" value="<?php echo $doc_id; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="fees" class="form-label">Fees</label>
                            <input type="number" class="form-control" id="fees" name="fees" value="<?php echo $row['fees']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="specialities" class="form-label">Specialities (comma separated)</label>
                            <input type="text" class="form-control" id="specialities" name="specialities" value="<?php echo $row['Specialities']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="degrees" class="form-label">Degrees (comma separated)</label>
                            <input type="text" class="form-control" id="degrees" name="degrees" value="<?php echo $row['Degrees']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="institutes" class="form-label">Institutes (comma separated)</label>
                            <input type="text" class="form-control" id="institutes" name="institutes" value="<?php echo $row['Institutes']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone_numbers" class="form-label">Phone Numbers (comma separated)</label>
                            <input type="text" class="form-control" id="phone_numbers" name="phone_number" value="<?php echo $row['Phone_Numbers']; ?>" required>
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                    </form>
                </section>
            </main>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        </body>

        </html>
<?php
    } else {
        echo "<script>alert('No data found for the specified doctor ID.');
        window.location.href='http://localhost/Hospital-Management/user/doctor.php';
        </script>
        ";
    }
} else {
    echo "<script>alert('No doctor id specified.');
    window.location.href='http://localhost/Hospital-Management/user/doctor.php';
    </script>
    ";
}
?>