<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Request a Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">📚 Library System</a>
    <div>
        <a href="index.php" class="btn btn-outline-light btn-sm">Home</a>
        <a href="request_book.php" class="btn btn-warning btn-sm">Request a Book</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4>Request a New Book</h4>
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
                                while($member = $member_result->fetch_assoc()) {
                                    echo "<option value='" . $member['member_id'] . "'>" . $member['name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Book Title:</label>
                            <input type="text" name="book_title" class="form-control" required placeholder="Enter book name">
                        </div>
                        <div class="mb-3">
                            <label>Author Name:</label>
                            <input type="text" name="author_name" class="form-control" required placeholder="Enter author name">
                        </div>
                        <button type="submit" name="submit" class="btn btn-warning w-100">Submit Request</button>
                    </form>

                    <?php
                    if(isset($_POST['submit'])) {
                        $member_id = $_POST['member_id'];
                        $book_title = mysqli_real_escape_string($conn, $_POST['book_title']);
                        $author_name = mysqli_real_escape_string($conn, $_POST['author_name']);

                        $sql = "INSERT INTO book_requests (member_id, book_title, author_name, request_date) 
                                VALUES ($member_id, '$book_title', '$author_name', CURDATE())";
                        
                        if ($conn->query($sql) === TRUE) {
                            echo "<div class='alert alert-success mt-3'>Request submitted successfully! Admin will review it soon.</div>";
                        } else {
                            echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
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