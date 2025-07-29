<?php
require __DIR__ . '/include/conn.php';
require __DIR__ . '/include/session_check.php';
require "layout/head.php";
// Check if the ID parameter is passed in the URL query string
if (isset($_GET['id'])) {
    // Get the user ID from the query string
    $id_pengguna = $_GET['id'];

    // Query to fetch the user details
    $query = "SELECT id_pengguna, name, username, roles_id FROM pengguna WHERE id_pengguna = ?";
    if ($stmt = $db->prepare($query)) {
        // Bind the ID parameter
        $stmt->bind_param("i", $id_pengguna);
        // Execute the query
        $stmt->execute();
        // Get the result
        $result = $stmt->get_result();

        // If the user exists
        if ($result->num_rows > 0) {
            // Fetch the user's data
            $user = $result->fetch_assoc();
        } else {
            // User not found, redirect with an error
            header("Location: user_data.php?status=error");
            exit();
        }

        // Close the statement
        $stmt->close();
    }
} else {
    // If no ID is set, redirect back to the user data page
    header("Location: user_data.php?status=error");
    exit();
}

// Process form submission to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $roles_id = $_POST['roles_id'];  // Get the new role

    // Check if any of the fields are empty
    if (empty($name) || empty($username)) {
        $error = "All fields are required!";
    } else {
        // Query to update the user details
        $update_query = "UPDATE pengguna SET name = ?, username = ?, roles_id = ? WHERE id_pengguna = ?";

        if ($stmt = $db->prepare($update_query)) {
            // Bind the parameters
            $stmt->bind_param("ssii", $name, $username, $roles_id, $id_pengguna);
            // Execute the query
            if ($stmt->execute()) {
                // Success: Redirect back to the previous page
                // header("Location: owner_data_pengguna.php?status=updated");
                header("Location: owner_data_pengguna.php");
                exit();
            } else {
                // Failed to update
                $error = "Error: Could not update the user.";
            }
            // Close the statement
            $stmt->close();
        } else {
            $error = "Error: Could not prepare the update query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengguna</title>
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
            <h3>Edit Pengguna</h3>
        </div>

        <div class="page-content">
            <section class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit Data Pengguna</h4>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form action="edit_pengguna.php?id=<?php echo $user['id_pengguna']; ?>" method="POST">
                                <div class="form-group">
                                    <label for="name">Name:</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" name="username" id="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="roles_id">Jabatan:</label>
                                    <select name="roles_id" id="roles_id" class="form-control">
                                        <option value="1" <?php echo ($user['roles_id'] == 1) ? 'selected' : ''; ?>>Owner</option>
                                        <option value="2" <?php echo ($user['roles_id'] == 2) ? 'selected' : ''; ?>>Administrasi</option>
                                        <option value="3" <?php echo ($user['roles_id'] == 3) ? 'selected' : ''; ?>>No Role</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary">Update Pengguna</button>
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
