<?php 
session_start();
include 'db.php'; 

if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}


if(isset($_GET['action']) && isset($_GET['req_id'])) {
    $req_id = $_GET['req_id'];
    $action = $_GET['action'];
    if($action == 'approve') {
        $conn->query("UPDATE book_requests SET status = 'Ordered' WHERE request_id = $req_id");
    } elseif($action == 'reject') {
        $conn->query("UPDATE book_requests SET status = 'Rejected' WHERE request_id = $req_id");
    }
    echo "<script>window.location.href='admin_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS for Modal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        <!-- Add Book Form with API -->
        <div class="col-md-5">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h4>Add New Book (Live API)</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label>Search By:</label>
                            <div class="input-group mb-2">
                                <select id="search_type" class="form-select" style="max-width: 120px;">
                                    <option value="title">Title</option>
                                    <option value="author">Author</option>
                                </select>
                                <input type="text" name="title" id="book_title" class="form-control" required placeholder="Type name...">
                                <button type="button" class="btn btn-info text-white fw-bold" onclick="fetchBookData()">Search via API</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Book Title:</label>
                            <input type="text" name="final_title" id="final_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Author Name:</label>
                            <input type="text" name="author_name" id="author_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Category ID (1 to 8):</label>
                            <input type="number" name="category_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>ISBN:</label>
                            <input type="text" name="isbn" id="isbn" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Cover Image URL (Auto-filled):</label>
                            <input type="text" name="cover_image" id="cover_image" class="form-control" readonly>
                            <!-- Image Preview with Click to Zoom feature -->
                            <img id="preview_image" src="" style="max-height: 150px; margin-top: 10px; display: none; border-radius: 5px; cursor: pointer;" onclick="showBigImage()" title="Click to view full size">
                        </div>
                        <button type="submit" name="add_book" class="btn btn-primary w-100">Add Book</button>
                    </form>

                    <?php
                    if(isset($_POST['add_book'])) {
                        $title = mysqli_real_escape_string($conn, $_POST['final_title']); 
                        $author_name = mysqli_real_escape_string($conn, $_POST['author_name']);
                        $category_id = $_POST['category_id'];
                        $isbn = mysqli_real_escape_string($conn, $_POST['isbn']);
                        $cover_image = mysqli_real_escape_string($conn, $_POST['cover_image']);

                      
                        $check_author = $conn->query("SELECT author_id FROM author WHERE author_name LIKE '%$author_name%' LIMIT 1");
                        
                        if($check_author->num_rows > 0) {
                            $author_data = $check_author->fetch_assoc();
                            $author_id = $author_data['author_id'];
                        } else {
                           
                            $conn->query("INSERT INTO author (author_name) VALUES ('$author_name')");
                            $author_id = $conn->insert_id;
                        }

                        
                        $sql = "INSERT INTO book (title, category_id, author_id, publisher_id, isbn, cover_image) 
                                VALUES ('$title', $category_id, $author_id, 1, '$isbn', '$cover_image')";
                        
                        if ($conn->query($sql) === TRUE) {
                            echo "<div class='alert alert-success mt-3'>Book added successfully with API data!</div>";
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
            <div class="card shadow mb-4">
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

    <!-- Book Requests Section -->
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4>Book Requests from Members</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Member</th>
                                <th>Requested Book</th>
                                <th>Author</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $req_sql = "SELECT r.*, m.name AS member_name FROM book_requests r 
                                        JOIN member m ON r.member_id = m.member_id ORDER BY r.request_id DESC";
                            $req_result = $conn->query($req_sql);

                            if ($req_result->num_rows > 0) {
                                while($req = $req_result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $req['member_name'] . "</td>";
                                    echo "<td>" . $req['book_title'] . "</td>";
                                    echo "<td>" . $req['author_name'] . "</td>";
                                    echo "<td>" . $req['request_date'] . "</td>";
                                    
                                    $status_color = 'secondary';
                                    if($req['status'] == 'Ordered') $status_color = 'success';
                                    if($req['status'] == 'Rejected') $status_color = 'danger';
                                    
                                    echo "<td><span class='badge bg-$status_color'>" . $req['status'] . "</span></td>";
                                    echo "<td>";
                                    if($req['status'] == 'Pending') {
                                        echo "<a href='admin_dashboard.php?action=approve&req_id=" . $req['request_id'] . "' class='btn btn-success btn-sm'>Approve</a> ";
                                        echo "<a href='admin_dashboard.php?action=reject&req_id=" . $req['request_id'] . "' class='btn btn-danger btn-sm'>Reject</a>";
                                    } else {
                                        echo "<span class='text-muted fw-bold'>Action Taken</span>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No requests yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Image Zoom Modal with Download & Copy -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Book Cover</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modal_image" src="" class="img-fluid mb-3" alt="Book Cover">
        
        <!-- Download & Copy Buttons -->
        <div class="d-flex justify-content-center gap-2">
            <button onclick="downloadImage()" class="btn btn-success">⬇️ Download Image</button>
            <button onclick="copyImage()" class="btn btn-primary">📋 Copy Image</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript for Open Library API & Image Zoom -->
<script>
function fetchBookData() {
    let searchTerm = document.getElementById('book_title').value;
    let searchType = document.getElementById('search_type').value;
    
    if(searchTerm === "") {
        alert("Please enter a name first.");
        return;
    }

    let url = "";
    if(searchType === "title") {
        url = "https://openlibrary.org/search.json?title=" + encodeURIComponent(searchTerm) + "&limit=1";
    } else {
        url = "https://openlibrary.org/search.json?author=" + encodeURIComponent(searchTerm) + "&limit=1";
    }

    fetch(url)
    .then(response => response.json())
    .then(data => {
        if(data.docs && data.docs.length > 0) {
            let bookInfo = data.docs[0];
            
            document.getElementById('final_title').value = bookInfo.title;
            let author = bookInfo.author_name ? bookInfo.author_name[0] : 'Unknown';
            document.getElementById('author_name').value = author;
            let isbn = bookInfo.isbn ? bookInfo.isbn[0] : 'N/A';
            document.getElementById('isbn').value = isbn;

            if(bookInfo.cover_i) {
                let imgUrl = "https://covers.openlibrary.org/b/id/" + bookInfo.cover_i + "-M.jpg";
                document.getElementById('cover_image').value = imgUrl;
                
                let imgPreview = document.getElementById('preview_image');
                imgPreview.src = imgUrl;
                imgPreview.style.display = "block";
            } else {
                document.getElementById('cover_image').value = "";
                document.getElementById('preview_image').style.display = "none";
            }

        } else {
            alert("No results found! Try a different name.");
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("API se data fetch karne mein error aaya. Check your internet.");
    });
}


function showBigImage() {
    let imgUrl = document.getElementById('preview_image').src;
    if(imgUrl) {
        document.getElementById('modal_image').src = imgUrl;
        var myModal = new bootstrap.Modal(document.getElementById('imageModal'));
        myModal.show();
    }
}


function downloadImage() {
    let imgUrl = document.getElementById('modal_image').src;
    if(imgUrl) {
        let link = document.createElement('a');
        link.href = imgUrl;
        link.download = 'Book_Cover.jpg';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}


function copyImage() {
    let imgUrl = document.getElementById('modal_image').src;
    fetch(imgUrl)
        .then(response => response.blob())
        .then(blob => {
            const item = new ClipboardItem({ "image/png": blob });
            navigator.clipboard.write([item]).then(function() {
                alert("Image copied to clipboard successfully!");
            }).catch(function(error) {
                alert("Failed to copy image. Your browser might not support it.");
            });
        })
        .catch(error => {
            alert("Error fetching image to copy.");
        });
}
</script>

</body>
</html>