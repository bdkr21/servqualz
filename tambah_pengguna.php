<?php
// Ensure the database connection is available
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $roles_id = $_POST['roles_id'];
    $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Check if any of the fields are empty
    if (empty($name) || empty($username) || empty($password)) {
        $error = "All fields are required!";
    } else {
        // Prepare the INSERT query
        $query = "INSERT INTO pengguna (name, username, password, roles_id) VALUES (?, ?, ?, ?)";

        // Prepare the statement
        if ($stmt = $db->prepare($query)) {
            // Bind parameters
            $stmt->bind_param("sssi", $name, $username, $password_hash, $roles_id);

            // Execute the query
            if ($stmt->execute()) {
                // Success: Redirect to user data page
                header("Location: owner_data_pengguna.php");
                exit();
            } else {
                // Error: Query failed
                $error = "Error: Could not execute the query.";
            }

            // Close the statement
            $stmt->close();
        } else {
            // Error: Query preparation failed
            $error = "Error: Could not prepare the query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna</title>
</head>
<body>

<div id="app">
    <?php require "layout/sidebar.php"; ?>

    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <h3>Tambah Pengguna</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Formulir Tambah Pengguna</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="tambah_pengguna.php" method="POST">
                                <div class="form-group">
                                    <label for="name">Name:</label>
                                    <input type="text" name="name" id="name" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" name="username" id="username" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <input type="password" name="password" id="password" class="form-control" required>
                                </div>  
                                <div class="form-group">
                                    <label for="roles_id">Jabatan:</label>
                                    <select name="roles_id" id="roles_id" class="form-control" required>
                                        <option value="1">Owner</option>
                                        <option value="2">Administrasi</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Tambah Pengguna</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div> <!-- End page-content -->

    </div> <!-- End main -->
</div> <!-- End app -->

<?php require "layout/js.php"; ?>
</body>
</html>
