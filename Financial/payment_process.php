<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
}
include ('../Admin/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pat_id = $_POST['pat_id'];
    $pay_id = $_POST['pay_id'];
    $pay_option = isset($_POST['pay_option']) ? $_POST['pay_option'] : null;
    $amount = $_POST['amount'];
    $ins_id = isset($_POST['installment_id']) ? $_POST['installment_id'] : null;
    $num_ins = isset($_POST['num_installments']) ? $_POST['num_installments'] : null;

    $conn->begin_transaction();
    try {
        // Fetch total cost
        $sql = "SELECT tot_cost FROM billing WHERE pat_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $pat_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $billing = $result->fetch_assoc();
            $tot_cost = $billing['tot_cost'];
        } else {
            throw new Exception('Patient does not exist');
        }

        // Handle payment options
        if ($pay_option === "clear") {

            //Check if admission id and release of date exist
            $sql = "SELECT adm_id, date_of_release FROM adm_pat WHERE pat_id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $pat_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            if ($result->num_rows > 0) {
                $adm_id = $row['adm_id'];
                $release_date = $row['date_of_release'];
                if ($release_date == '0000-00-00') {
                    echo "Release date needs to be updated <br>";
                    throw new Exception("Update the date of release");
                }
            }

            //check if the total amount is matched 
            if ($amount == $tot_cost) {
                $sql = "INSERT INTO payment (pay_id, pay_status, date, pat_id) VALUES (?, 'clear', NOW(), ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $pay_id, $pat_id);
                if ($stmt->execute()) {
                    echo "<script>alert('Payment Success')</script>";
                } else {
                    throw new Exception(".$stmt->error.");
                }
            } else {
                throw new Exception('Amount does not match the total cost.');
            }
        }

        // check if the payment option is installment
        else if ($pay_option === "installment" && $num_ins >= 5 && $num_ins <= 10) {
            $sql = "INSERT INTO payment (pay_id, pay_status, date, pat_id) VALUES (?, 'installment', NOW(), ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $pay_id, $pat_id);
            if ($stmt->execute()) {
                // Calculate installment details
                $ins_count = 1;
                $amountRem = $tot_cost - $amount;
                $nextInsAmount = $amountRem / ($num_ins - 1);

                $sql = "INSERT INTO installment (ins_id, tot_ins, ins_count, amount_rem, next_ins_amount, pay_id) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("siiiss", $ins_id, $num_ins, $ins_count, $amountRem, $nextInsAmount, $pay_id);
                if ($stmt->execute()) {
                    $sql = "INSERT INTO ins_amount (ins_id, ins_num, ins_amount) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sis", $ins_id, $ins_count, $amount);
                    $stmt->execute();

                    echo "<script>alert('Installment Payment Initialized.');
                    window.location.href=' http://localhost/Hospital-Management/financial/payment.php'</script>";
                } else {
                    throw new Exception('Error initializing installment payment.');
                }
            } else {
                throw new Exception('Error inserting into payment.');
            }
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error: " . $e->getMessage() . "');
        window.location.href=' http://localhost/Hospital-Management/financial/payment.php'
        </script>
        ";
    }
    $stmt->close();


}
