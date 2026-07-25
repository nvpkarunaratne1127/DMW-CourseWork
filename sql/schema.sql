-- ============================================================
-- Student Library Book Reservation System - Database Schema
-- ============================================================
-- Import this file in phpMyAdmin (XAMPP) OR run:
--   mysql -u root -p < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS library_system;
USE library_system;

-- ---------------------------------------------
-- Table: admins (Librarian login accounts)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Default login -> username: admin | password: admin123
INSERT INTO admins (username, password) VALUES
('admin', '$2b$10$uC5RmpFtX45z0D2pk/fYPOaO6WEIAZ3XzgXrRo7X6eMdyQ3VC0k0u');

-- ---------------------------------------------
-- Table: books
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(100) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    genre VARCHAR(50) NOT NULL,
    total_copies INT NOT NULL DEFAULT 1,
    available_copies INT NOT NULL DEFAULT 1,
    cover_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------
-- Table: reservations
-- (book_id is a foreign key -> books.id : proper normalization)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    student_name VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    request_date DATE NOT NULL,
    return_date DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Reserved',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reservation_book
        FOREIGN KEY (book_id) REFERENCES books(id)
        ON DELETE CASCADE
);

-- ---------------------------------------------
-- Sample data (optional - feel free to delete)
-- ---------------------------------------------
INSERT INTO books (title, author, isbn, genre, total_copies, available_copies) VALUES
('The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 'Fiction', 3, 3),
('Clean Code', 'Robert C. Martin', '9780132350884', 'Technology', 2, 2),
('A Brief History of Time', 'Stephen Hawking', '9780553380163', 'Science', 4, 4);
