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

-- ---------------------------------------------
-- Sample reservations
-- ---------------------------------------------
INSERT INTO reservations
(book_id, student_name, telephone, request_date, return_date, status)
VALUES

-- Book 1 (1 reservation)
(1, 'Nimal Perera', '0712345678', '2026-07-20', '2026-08-03', 'Reserved'),

-- Book 2 (1 reservation)
(2, 'Kumari Silva', '0723456789', '2026-07-21', '2026-08-04', 'Reserved'),

-- Book 3 (2 reservations)
(3, 'Sunil Fernando', '0774567890', '2026-07-18', '2026-08-01', 'Reserved'),
(3, 'Dilani Jayasinghe', '0755678901', '2026-07-19', '2026-08-02', 'Reserved'),

-- Book 4 (2 reservations)
(4, 'Kasun Wijesinghe', '0766789012', '2026-07-17', '2026-07-31', 'Reserved'),
(4, 'Tharushi Gunawardena', '0787890123', '2026-07-22', '2026-08-05', 'Reserved'),

-- Book 5 (2 reservations)
(5, 'Chamara Ranasinghe', '0708901234', '2026-07-16', '2026-07-30', 'Reserved'),
(5, 'Ishara Samarasinghe', '0719012345', '2026-07-23', '2026-08-06', 'Reserved'),

-- Book 7 (4 reservations)
(7, 'Dinesh Karunaratne', '0720123456', '2026-07-15', '2026-07-29', 'Reserved'),
(7, 'Anusha Ekanayake', '0771234567', '2026-07-18', '2026-08-01', 'Reserved'),
(7, 'Roshan Abeysekera', '0752345678', '2026-07-20', '2026-08-03', 'Reserved'),
(7, 'Sanduni Herath', '0763456789', '2026-07-22', '2026-08-05', 'Reserved'),

-- Book 8 (5 reservations)
(8, 'Pradeep Senanayake', '0784567890', '2026-07-14', '2026-07-28', 'Reserved'),
(8, 'Nadeesha Wickramasinghe', '0705678901', '2026-07-15', '2026-07-29', 'Reserved'),
(8, 'Mahesh Bandara', '0716789012', '2026-07-17', '2026-07-31', 'Reserved'),
(8, 'Sachini Madushani', '0727890123', '2026-07-19', '2026-08-02', 'Reserved'),
(8, 'Lakshan Peris', '0778901234', '2026-07-21', '2026-08-04', 'Reserved'),

-- Book 9 (5 reservations)
(9, 'Harsha Rajapaksha', '0759012345', '2026-07-13', '2026-07-27', 'Reserved'),
(9, 'Udesh Jayawardena', '0760123456', '2026-07-14', '2026-07-28', 'Reserved'),
(9, 'Thilini Fernando', '0781234567', '2026-07-16', '2026-07-30', 'Reserved'),
(9, 'Gayan De Silva', '0702345678', '2026-07-18', '2026-08-01', 'Reserved'),
(9, 'Malsha Perera', '0713456789', '2026-07-20', '2026-08-03', 'Reserved');

