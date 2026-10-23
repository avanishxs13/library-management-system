CREATE DATABASE IF NOT EXISTS library_db;
USE library_db;

CREATE TABLE category (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL
);

CREATE TABLE publisher (
  publisher_id INT AUTO_INCREMENT PRIMARY KEY,
  publisher_name VARCHAR(150) NOT NULL,
  city VARCHAR(100)
);

CREATE TABLE author (
  author_id INT AUTO_INCREMENT PRIMARY KEY,
  author_name VARCHAR(150) NOT NULL,
  country VARCHAR(100)
);

CREATE TABLE book (
  book_id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  category_id INT, 
  author_id INT, 
  publisher_id INT,
  isbn VARCHAR(20) UNIQUE, 
  price DECIMAL(8,2),
  FOREIGN KEY (category_id) REFERENCES category(category_id),
  FOREIGN KEY (author_id) REFERENCES author(author_id),
  FOREIGN KEY (publisher_id) REFERENCES publisher(publisher_id)
);

CREATE TABLE book_copy (
  copy_id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT, 
  copy_code VARCHAR(20) UNIQUE,
  status ENUM('Available','Issued','Lost') DEFAULT 'Available',
  FOREIGN KEY (book_id) REFERENCES book(book_id)
);

CREATE TABLE member (
  member_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL, 
  email VARCHAR(150) UNIQUE,
  phone VARCHAR(15), 
  join_date DATE,
  status ENUM('Active','Inactive') DEFAULT 'Active'
);

CREATE TABLE loan (
  loan_id INT AUTO_INCREMENT PRIMARY KEY,
  copy_id INT, 
  member_id INT,
  issue_date DATE NOT NULL, 
  due_date DATE NOT NULL, 
  return_date DATE,
  FOREIGN KEY (copy_id) REFERENCES book_copy(copy_id),
  FOREIGN KEY (member_id) REFERENCES member(member_id)
);

CREATE TABLE fine (
  fine_id INT AUTO_INCREMENT PRIMARY KEY,
  loan_id INT, 
  amount DECIMAL(6,2),
  paid BOOLEAN DEFAULT FALSE,
  FOREIGN KEY (loan_id) REFERENCES loan(loan_id)
);

INSERT INTO category (category_name) VALUES ('Science'), ('Fiction'), ('History'), ('Technology'), ('Biography'), ('Comics'), ('Business'), ('Self-Help');

INSERT INTO author (author_name, country) VALUES 
('R.K. Narayan', 'India'), ('APJ Abdul Kalam', 'India'), ('Chetan Bhagat', 'India'), 
('J.K. Rowling', 'UK'), ('Yuval Noah Harari', 'Israel'), ('Agatha Christie', 'UK'), 
('George Orwell', 'UK'), ('Dan Brown', 'USA'), ('Stephen Hawking', 'UK'), ('Savi Sharma', 'India');

INSERT INTO publisher (publisher_name, city) VALUES 
('Penguin', 'Delhi'), ('Oxford University Press', 'Mumbai'), ('Rupa Publications', 'Kolkata'), 
('Bloomsbury', 'London'), ('HarperCollins', 'Noida'), ('Westland', 'Chennai');

INSERT INTO book (title, category_id, author_id, publisher_id, isbn, price) VALUES 
('Wings of Fire', 5, 2, 1, 'ISBN001', 350.00), ('Malgudi Days', 2, 1, 1, 'ISBN002', 250.00), 
('Harry Potter', 2, 4, 4, 'ISBN003', 499.00), ('Sapiens', 3, 5, 2, 'ISBN004', 599.00), 
('Five Point Someone', 2, 3, 3, 'ISBN005', 199.00), ('Murder on the Orient Express', 2, 6, 5, 'ISBN006', 299.00), 
('1984', 2, 7, 1, 'ISBN007', 399.00), ('The Da Vinci Code', 2, 8, 4, 'ISBN008', 450.00), 
('A Brief History of Time', 1, 9, 4, 'ISBN009', 550.00), ('Everyone Has a Story', 8, 10, 6, 'ISBN010', 150.00);

INSERT INTO book_copy (book_id, copy_code, status) VALUES 
(1, 'WOF-01', 'Available'), (1, 'WOF-02', 'Available'), (2, 'MD-01', 'Available'), 
(3, 'HP-01', 'Available'), (3, 'HP-02', 'Available'), (4, 'SAP-01', 'Available'), 
(5, 'FPS-01', 'Available'), (6, 'MOE-01', 'Available'), (7, '1984-01', 'Available'), 
(8, 'DVC-01', 'Available');

INSERT INTO member (name, email, phone, join_date, status) VALUES 
('Rahul Sharma', 'rahul@mail.com', '9876543210', '2025-01-10', 'Active'), 
('Priya Verma', 'priya@mail.com', '9876543211', '2025-02-15', 'Active'), 
('Amit Singh', 'amit@mail.com', '9876543212', '2025-03-01', 'Active');

CREATE VIEW vw_available_books AS
SELECT b.title, bc.copy_code, c.category_name, a.author_name
FROM book_copy bc
JOIN book b ON bc.book_id = b.book_id
JOIN category c ON b.category_id = c.category_id
JOIN author a ON b.author_id = a.author_id
WHERE bc.status = 'Available';


