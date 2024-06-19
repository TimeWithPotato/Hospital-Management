<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include '../Admin/connect.php';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $conn->begin_transaction();

    try{
        $pay_id = $_POST['pay_id'];
        $ins_id = $_POST['ins_id'];
        $ins_num = $_POST['ins_num'];
        $amount = $_POST['amount'];
        $pay_status = "";
    
        $sql = "SELECT ins_id FROM installment WHERE ins_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $ins_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows <=0 ){
            throw new Exception("Installment ID does not exist");
        }

        $sql = "SELECT pay_status FROM payment WHERE pay_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $pay_id);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $rows = $result->fetch_assoc();
            $pay_status = $rows['pay_status'];
        } else {
            throw new Exception('No payment details found');
        }
        if ($pay_status == 'installment') {
            $sql = "SELECT tot_ins, ins_count, amount_rem FROM installment WHERE ins_id = ? AND pay_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $ins_id, $pay_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $rows = $result->fetch_assoc();

                //check if the installment number and installment count is equal
                
                if($rows !== null && (($rows['tot_ins'] == $rows['ins_count']) || $rows['amount_rem'] <= 0)){

                    throw new Exception('All installment paid');
                }


                $tot_ins = $rows['tot_ins'];
                $ins_count = $rows['ins_count'];
                $amountRem = $rows['amount_rem'] - $amount;
                $nextInsAmount = $amountRem / ($tot_ins - ($ins_count + 1));
                $ins_count++;
                $sql = "UPDATE installment SET ins_count = ?, amount_rem = ?, next_ins_amount = ? WHERE ins_id = ? AND pay_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iiiss", $ins_count, $amountRem, $nextInsAmount, $ins_id, $pay_id);
                $stmt->execute();
    
                $sql = "INSERT INTO ins_amount (ins_id, ins_num, ins_amount) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sii", $ins_id, $ins_count, $amount);
                $stmt->execute();
    
                echo "<script>alert('Installment Payment Processed.')</script>";
            } else {
                throw new Exception('No installment information found for the payment.');
            }
        }

        $conn->commit();
        
    }catch(Exception $e){
       
        $conn->rollback();
        echo "<div class='alert alert-danger error'>
        <span class='close-btn'>&times;</span>
        <p>Error: ". $e->getMessage()."</p>
      </div>";

    }


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
<main>
    <section class="container">
        <h1 class="text-center" style="margin-top:15vh;">Installment Payment Form</h1>
    <form action="pay_installment.php" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" placeholder="pat_id" name="pay_id">
                    <label for="floatingInput">Payment ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="floatingInput" placeholder="pat_id" name="ins_id">
                    <label for="floatingInput">Installment ID</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="floatingInput" placeholder="pat_id" name="ins_num">
                    <label for="floatingInput">Installment Number</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="floatingInput" placeholder="pat_id" name="amount">
                    <label for="floatingInput">Installment Amount</label>
                </div>
                <button type="submit" class="btn btn-primary">Pay Installment</button>
    </form>
    </section>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../js/utilities.js"></script>
</body>
</html>