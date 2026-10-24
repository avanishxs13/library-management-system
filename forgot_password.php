<?php 
session_start();
include 'db.php'; 

$step = 1; 
$username = '';
$question = '';

// Step 1: Check if username exists
if(isset($_POST['check_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $sql = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        $question = $admin['security_question']; // Fetch question from DB
        $step = 2; 
    } else {
        echo "<script>alert('Username not found!');</script>";
    }
}

// Step 2: Verify Answer and Reset Password
if(isset($_POST['verify_answer'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $answer = mysqli_real_escape_string($conn, $_POST['answer']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $question = $_POST['question']; // Keep question visible

    // Check answer
    $sql = "SELECT * FROM admin WHERE username = '$username' AND security_answer = '$answer'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Answer correct, update password
        $update_sql = "UPDATE admin SET password = '$new_password' WHERE username = '$username'";
        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('Password reset successfully! Please login.'); window.location.href='login.php';</script>";
        } else {
            echo "<div class='alert alert-danger'>Error updating password: " . $conn->error . "</div>";
        }
    } else {
        echo "<script>alert('Incorrect answer! Try again.');</script>";
        $step = 2; // Stay on step 2
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
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
                <div class="card-header bg-warning text-dark text-center">
                    <h4>Reset Password</h4>
                </div>
                <div class="card-body">
                    
                    <?php if($step == 1): ?>
                    <!-- Step 1: Ask Username -->
                    <form method="POST" action="">
                        <p class="text-muted">Enter your username to reset your password.</p>
                        <div class="mb-3">
                            <label>Username:</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <button type="submit" name="check_user" class="btn btn-warning w-100">Next</button>
                    </form>

                    <?php elseif($step == 2): ?>
                    <!-- Step 2: Security Question & New Password -->
                    <form method="POST" action="">
                        <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>">
                        <input type="hidden" name="question" value="<?php echo htmlspecialchars($question); ?>">
                        
                        <div class="mb-3">
                            <label>Security Question:</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($question); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label>Your Answer:</label>
                            <input type="text" name="answer" class="form-control" required placeholder="Enter your answer">
                        </div>
                        <div class="mb-3">
                            <label>New Password:</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <button type="submit" name="verify_answer" class="btn btn-success w-100">Reset Password</button>
                    </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>