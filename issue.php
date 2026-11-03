<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Issue Book</title>
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
                <div class="card-header bg-primary text-white">
                    <h4>Issue a Book</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Select Member:</label>
                            <select name="member_id" class="form-control" required>
                                <option value="">-- Select Member --</option>
                                <?php
                                $member_sql = "SELECT member_id, name FROM member";
                                $member_result = $conn->query($member_sql);
                                if ($member_result->num_rows > 0) {
                                    while($member = $member_result->fetch_assoc()) {
                                        echo "<option value='" . $member['member_id'] . "'>" . $member['name'] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No members found</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Book Copy Code (e.g., HP-01):</label>
                            <input type="text" name="copy_code" class="form-control" required>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary w-100">Issue Book</button>
                    </form>

                    <?php
                    if(isset($_POST['submit'])) {
                        $member_id = $_POST['member_id'];
                        $copy_code = trim($_POST['copy_code']);

                        
                        $check_limit_sql = "SELECT COUNT(*) AS total_books FROM loan 
                                            WHERE member_id = $member_id AND return_date IS NULL";
                        $limit_result = $conn->query($check_limit_sql);
                        $limit_data = $limit_result->fetch_assoc();
                        $current_books = $limit_data['total_books'];

                        if ($current_books >= 3) {
                           
                            echo "<div class='alert alert-warning mt-3'><b>Limit Reached:</b> This member already has 3 books issued. Please return a book first to issue a new one.</div>";
                        } else {
                            
                            $check_book_sql = "SELECT copy_id, status FROM book_copy WHERE copy_code = '$copy_code'";
                            $book_result = $conn->query($check_book_sql);

                            if ($book_result->num_rows == 0) {
                                echo "<div class='alert alert-danger mt-3'><b>Error:</b> Book Copy Code ($copy_code) not found in the database.</div>";
                            } else {
                                $book = $book_result->fetch_assoc();
                                
                                if ($book['status'] == 'Issued') {
                                    echo "<div class='alert alert-warning mt-3'><b>Sorry:</b> This book is already issued to someone else.</div>";
                                } else {
                                   
                                    $copy_id = $book['copy_id'];
                                    $issue_sql = "INSERT INTO loan (copy_id, member_id, issue_date, due_date) 
                                                  VALUES ($copy_id, $member_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))";
                                    
                                    if ($conn->query($issue_sql) === TRUE) {
                                        $update_sql = "UPDATE book_copy SET status = 'Issued' WHERE copy_id = $copy_id";
                                        $conn->query($update_sql);
                                        
                                        echo "<div class='alert alert-success mt-3'><b>Success:</b> Book issued successfully!</div>";
                                    } else {
                                        echo "<div class='alert alert-danger mt-3'><b>Database Error:</b> " . $conn->error . "</div>";
                                    }
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