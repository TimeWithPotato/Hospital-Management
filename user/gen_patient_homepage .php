<?php
include ('../Admin/connect.php');
session_start();
if (!isset($_SESSION['email'])) {
    header('location:Login.php');
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
                    <a class="navbar-brand" href="http://localhost/Hospital-Management/user/gen_patient_homepage.php">DoctorBari</a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="http://localhost/Hospital-Management/user/general_patient.php">Profile</a>
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
                                                    <input name="gen_pat_email" type="text" class="form-control" id="floatingEmail" placeholder="email">
                                                    <label for="floatingEmail">Email</label>
                                                </div>
                                                <div class="form-floating mb-3">
                                                    <input name="gen_pat_password" type="text" class="form-control" id="floatingPassword" placeholder="new password">
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
    <main class="container" style="margin-top:20vh; margin-bottom:10vh;">
        <!-- department card -->
        <section>
            <div class="container">
                <div class="row gx-5 gy-5">
                    <div id="gen-pat-view-cardiology" class="card col me-3" style="width: 18rem;">
                        <img src="../assets/Cardiology.jpeg" class="card-img-top mt-3" alt="Cardiology">
                        <div class="card-body">
                            <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dicta tempora
                                pariatur asperiores, iusto libero quam, aut corrupti aliquam est ea nihil! Facere
                                dolorum ipsam voluptates excepturi hic beatae, dolor debitis.</p>
                        </div>
                    </div>
                    <div id="gen-pat-view-dermatology" class="card col me-3" style="width: 18rem;">
                        <img src="../assets/dermatology.png" class="card-img-top mt-3" alt="Dermatology">
                        <div class="card-body">
                            <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ipsam atque
                                earum exercitationem saepe, quidem voluptatum vero soluta labore facere commodi non
                                deserunt amet, ut aliquam aspernatur. Quasi velit alias numquam?</p>
                        </div>
                    </div>
                    <div id="gen-pat-view-neurology" class="card col" style="width: 18rem;">
                        <img src="../assets/neurology.jpg" class="card-img-top mt-3" alt="Neurology">
                        <div class="card-body">
                            <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ut, cum? Culpa
                                quasi, molestiae porro amet soluta excepturi vitae perspiciatis explicabo et sed sit
                                dolorum sint! Eveniet itaque ipsam delectus laudantium?</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/department.js"></script>
</body>

</html>