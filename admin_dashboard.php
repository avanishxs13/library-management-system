<?php 
session_start();
include 'db.php'; 

// Check karo ki admin login hai ya nahi
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">📚 Library System (Admin)</a>
    <div>
        <a href="index.php" class="btn btn-outline-light btn-sm">View Site</a>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <!-- Add Book Form -->
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4>Add New Book</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Book Title:</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Author ID (e.g., 1, 2, 3):</label>
                            <input type="number" name="author_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Category ID (e.g., 1, 2, 3):</label>
                            <input type="number" name="category_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>ISBN:</label>
                            <input type="text" name="isbn" class="form-control" required>
                        </div>
                        <button type="submit" name="add_book" class="btn btn-primary w-100">Add Book</button>
                    </form>

                    <?php
                    if(isset($_POST['add_book'])) {
                        $title = $_POST['title'];
                        $author_id = $_POST['author_id'];
                        $category_id = $_POST['category_id'];
                        $isbn = $_POST['isbn'];

                        // Publisher ID 1 default rakha hai
                        $sql = "INSERT INTO book (title, category_id, author_id, publisher_id, isbn) 
                                VALUES ('$title', $category_id, $author_id, 1, '$isbn')";
                        
                        if ($conn->query($sql) === TRUE) {
                            echo "<div class='alert alert-success mt-3'>Book added successfully!</div>";
                        } else {
                            echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Current Loans View -->
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4>Active Loans (Books Currently Issued)</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Member Name</th>
                                <th>Book Title</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT m.name AS member, b.title AS book, l.issue_date, l.due_date 
                                    FROM loan l
                                    JOIN member m ON l.member_id = m.member_id
                                    JOIN book_copy bc ON l.copy_id = bc.copy_id
                                    JOIN book b ON bc.book_id = b.book_id
                                    WHERE l.return_date IS NULL";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['member'] . "</td>";
                                    echo "<td>" . $row['book'] . "</td>";
                                    echo "<td>" . $row['issue_date'] . "</td>";
                                    echo "<td>" . $row['due_date'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>No active loans right now.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>