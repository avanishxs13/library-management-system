<?php 
session_start();
include 'db.php'; 

// Agar pehle se login hai, toh seedha dashboard par bhej do
if(isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
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
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-dark text-white text-center">
                    <h4>Admin Login</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Username:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-dark w-100">Login</button>
                    </form>

                    <!-- Forgot Password Link -->
                    <div class="text-center mt-3">
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>

                    <?php
                    if(isset($_POST['login'])) {
                        $username = $_POST['username'];
                        $password = $_POST['password'];

                        $username = mysqli_real_escape_string($conn, $username);
                        $password = mysqli_real_escape_string($conn, $password);

                        $sql = "SELECT * FROM admin WHERE username = '$username' AND password = '$password'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $_SESSION['admin_logged_in'] = true;
                            echo "<script>window.location.href='admin_dashboard.php';</script>";
                        } else {
                            echo "<div class='alert alert-danger mt-3'>Invalid Username or Password</div>";
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