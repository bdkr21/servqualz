<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/vendors/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/app.css">
    <link rel="stylesheet" href="assets/css/pages/auth.css">
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div id="auth" class="d-flex justify-content-center align-items-center vh-100" style="background: #f7f9fc;">
        <div class="col-lg-5 col-12">
            <div class="container w-100">
                <div id="auth-left" class="text-center">
                    <h1>NDAD PROFESSIONAL HAIRSTYLIST</h1><br>
                    <!-- <img src="assets/images/logocv.png" alt="Logo" class="mb-4" style="max-width: 250px;"> -->
                    <form action="login-act.php" method="post">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="text" class="form-control form-control-xl" placeholder="Username" name="identifier" required style="border-radius: 30px;">
                            <div class="form-control-icon">
                                <i class="bi bi-person" style="color: #007bff;"></i>
                            </div>
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl" placeholder="Password" name="password" required style="border-radius: 30px;">
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock" style="color: #007bff;"></i>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-4" style="border-radius: 30px;">Log in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalidlogin'): ?>
            Swal.fire({
                title: 'Error!',
                text: 'Invalid username or password.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    </script>
</body>
</html>
