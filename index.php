<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Library Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">📚 Library System</a>
    <div>
        <a href="index.php" class="btn btn-outline-light btn-sm">Available Books</a>
        <a href="issue.php" class="btn btn-outline-light btn-sm">Issue Book</a>
        <a href="return.php" class="btn btn-outline-light btn-sm">Return Book</a>
        <a href="request_book.php" class="btn btn-warning btn-sm">Request a Book</a>
        <a href="register.php" class="btn btn-success btn-sm">Register</a>
        <!-- Admin Login Button -->
        <a href="login.php" class="btn btn-warning btn-sm">Admin Login</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Available Books</h2>
        
        <!-- Search Form -->
        <form method="GET" action="" class="d-flex" style="width: 300px;">
            <input type="text" name="search" class="form-control me-2" placeholder="Search by Book or Author..." value="<?php if(isset($_GET['search'])) echo $_GET['search']; ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>Cover</th>
                <th>Book Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Copy Code</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Default query
            $sql = "SELECT * FROM vw_available_books";
            
            // Agar user ne kuch search kiya hai
            if(isset($_GET['search']) && $_GET['search'] != '') {
                $search = $conn->real_escape_string($_GET['search']);
                $sql = "SELECT * FROM vw_available_books 
                        WHERE title LIKE '%$search%' OR author_name LIKE '%$search%'";
            }

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    
                    // Image Column with Click to Zoom
                    echo "<td>";
                    if(!empty($row['cover_image'])) {
                        echo "<img src='" . $row['cover_image'] . "' alt='Cover' style='width: 60px; height: 80px; object-fit: cover; border-radius: 5px; cursor: pointer;' onclick=\"showBigImage('" . $row['cover_image'] . "')\" title='Click to view full size'>";
                    } else {
                        echo "<span class='text-muted'>No Image</span>";
                    }
                    echo "</td>";

                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['author_name'] . "</td>";
                    echo "<td>" . $row['category_name'] . "</td>";
                    echo "<td><span class='badge bg-success'>" . $row['copy_code'] . "</span></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No books found matching your search.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Image Zoom Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Book Cover</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modal_image" src="" class="img-fluid" alt="Book Cover">
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS for Modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Image ko bada dikhane ke liye function
function showBigImage(imageUrl) {
    if(imageUrl) {
        document.getElementById('modal_image').src = imageUrl;
        var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
        myModal.show();
    }
}
</script>

</body>
</html>