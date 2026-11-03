<?php 
session_start();
include 'db.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Member Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">📚 Library System</a>
  </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h4>New Member Registration</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Full Name:</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Phone:</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-success w-100">Register</button>
                    </form>

                    <?php
                    if(isset($_POST['register'])) {
                        $name = mysqli_real_escape_string($conn, $_POST['name']);
                        $email = mysqli_real_escape_string($conn, $_POST['email']);
                        $phone = mysqli_real_escape_string($conn, $_POST['phone']);

                      
                        $check_email = $conn->query("SELECT * FROM member WHERE email = '$email'");
                        
                        if($check_email->num_rows > 0) {
                            echo "<div class='alert alert-danger mt-3'>Email already registered! Please login or use another email.</div>";
                        } else {
                            $sql = "INSERT INTO member (name, email, phone, join_date, status) 
                                    VALUES ('$name', '$email', '$phone', CURDATE(), 'Active')";
                            
                            if ($conn->query($sql) === TRUE) {
                                echo "<div class='alert alert-success mt-3'>Registration successful! You can now issue books.</div>";
                            } else {
                                echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>