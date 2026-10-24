<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Return Book</title>
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
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4>Return a Book</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Enter Loan ID:</label>
                            <input type="number" name="loan_id" class="form-control" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-success w-100">Return Book</button>
                    </form>

                    <?php
                    if(isset($_POST['submit'])) {
                        $loan_id = $_POST['loan_id'];

                        // Check if Loan ID exists
                        $check_sql = "SELECT return_date, copy_id FROM loan WHERE loan_id = $loan_id";
                        $check_result = $conn->query($check_sql);

                        if ($check_result->num_rows == 0) {
                            echo "<div class='alert alert-danger mt-3'><b>Error:</b> Loan ID ($loan_id) not found in the database.</div>";
                        } else {
                            $loan_data = $check_result->fetch_assoc();
                            
                            if ($loan_data['return_date'] != NULL) {
                                echo "<div class='alert alert-warning mt-3'><b>Warning:</b> This book has already been returned.</div>";
                            } else {
                                // Process return
                                $copy_id = $loan_data['copy_id'];
                                
                                $return_sql = "UPDATE loan SET return_date = CURDATE() WHERE loan_id = $loan_id";
                                $update_book_sql = "UPDATE book_copy SET status = 'Available' WHERE copy_id = $copy_id";

                                if ($conn->query($return_sql) === TRUE && $conn->query($update_book_sql) === TRUE) {
                                    echo "<div class='alert alert-success mt-3'><b>Success:</b> Book returned successfully!</div>";
                                } else {
                                    echo "<div class='alert alert-danger mt-3'><b>Database Error:</b> " . $conn->error . "</div>";
                                }
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