<?php
// user_data.php

// Ensure the database connection is available
require __DIR__ . '/../include/conn.php';

// Query to fetch all users from 'pengguna' table
$query = "SELECT id_pengguna, name, username, roles_id FROM pengguna"; 
$result = $db->query($query);

// Check for query errors
if (!$result) {
    die('Query failed: ' . $db->error);
}
?>

<section class="row"> 
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Data Pengguna</h4>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Name</th>
                            <th scope="col">Username</th>
                            <th scope="col">Jabatan</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_number = 1;
                        // Loop through the results and display each user
                        while ($row = $result->fetch_assoc()) {
                            if ($row['roles_id'] == 1) {
                                $role = "Owner";
                            } elseif ($row['roles_id'] == 2) {
                                $role = "Administrasi";
                            } elseif ($row['roles_id'] == 0) {
                                $role = "TIdak Ada Jabatan";
                            } 

                            echo "<tr>
                                    <th scope='row'>{$row_number}</th>
                                    <td>{$row['name']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$role}</td>
                                    <td><a href='edit_pengguna.php?id={$row['id_pengguna']}'>Edit</a> | <a href='delete_pengguna.php?id={$row['id_pengguna']}'>Delete</a></td>
                                  </tr>";
                                   $row_number++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
