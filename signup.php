<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoctorBari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
           <!-- navbar start -->
            <section class="container">
                <nav class="navbar fixed-top navbar-expand-lg nav-body">
                    <div class="container">
                      <a class="navbar-brand" href="index.html">
                         <span class="doctor">Doctor</span>Bari
                      </a>
                      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                      </button>
                      <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                          <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="index.html">Home</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="signup.php">Sign Up</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" href="Login.php">Login</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </nav>
            </section>
           <!-- navbar end -->
    </header>
<main>
    <section class="container">
        <form action="otp-checker.php" method="POST" class="form-floating" style="margin-top:20vh;">
            <h1 class="text text-primary text-center fw-bold"><span class="fst-italic">S</span><span class="fst-italic fs-3 fw-bold text-danger">i</span>gnUP Form</h1>
            <div class="form-floating mb-3">
                <input name="email" type="email" class="form-control" id="userEmail" placeholder="name@example.com">
                <label for="floatingText">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input name="username" type="text" class="form-control" id="userName" placeholder="your name">
                <label for="floatingText">Your Name</label>
            </div>
            <div class="form-floating mb-3">
                <input name="password" type="password" class="form-control" id="userPassword" placeholder="name@example.com">
                <label for="floatingText">Your Password</label>
            </div>
            <button class="btn btn-primary" type="submit" name="signup" id="signup">SignUp</button>
        </form>
    </section>
</main>
<script>
  document.getElementById('signup').addEventListener('click',function(){
    const userEmailInput =document.getElementById('userEmail');
    const userEmail = userEmailInput.value;

    const userPasswordInput = document.getElementById('userPassword');
    const userPassword = userPasswordInput.value;
    const form = document.createElement('form');
    form.method='POST';
    form.action='http://localhost/Hospital-Management/otp-checker-form.php';

    const input1 =document.createElement('input');
    const input2 =document.createElement('input');
    input1.name = 'email';
    input1.type='hidden';
    input1.value=userEmail;

    input2.name='password';
    input2.type='hidden';
    input2.value=userPassword;

    form.appendChild(input1);
    form.appendChild(input2);
    document.appendChild(form);
    form.submit();


  })
</script>
</body>
</html>

