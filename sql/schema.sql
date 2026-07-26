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
-- Sample data (optional)
-- ---------------------------------------------
INSERT INTO books (title, author, isbn, genre, total_copies, available_copies, cover_image) VALUES
('Under the Whispering Door','T.J. Klune','9781250838148','Fiction',3,2,'book_6a643ecbe0392.jpg'),
('The Disappearing Spoon','Sam Kean','9780316051637','Science',2,1,'book_6a6441b45df91.jpg'),
('Clean Code','Robert Cecil Martin','9780135398524','Technology',5,3,'book_6a6445c2cba4d.jpg'),
('The Silent Patient','Alex Michaelides','9781250230782','Fiction',4,2,'book_6a64354aa17e5.jpg'),
('The Demon Haunted World','Carl Edward Sagan, Ann Druyan','9780307801043','Science',3,1,'book_6a64439c626b3.jpg'),
('The Selfish Gene','Richard Dawkins','9780191537554','Science',2,2,'book_6a644238da288.jpg'),
('CODE: The Hidden Language of Computer Hardware and Software (Second Edition)','Charles Petzold','9780137909100','Technology',6,2,'book_6a6444495dc00.jpg'),
('Learning PHP, MySQL & JavaScript','Robin Nixon','9781098152314','Technology',5,0,'book_6a6448279d523.jpg'),
('Crime and Punishment','Fyodor Dostoevsky','9783863523756','Fiction',8,3,'book_6a643941352cb.jpg');
