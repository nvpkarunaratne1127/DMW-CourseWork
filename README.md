# Student Library Book Reservation System

A PHP + MySQL web application (Scenario B) built with plain PHP, mysqli (prepared statements),
and Bootstrap 5 for a responsive UI.

## Features

**Admin Portal (Librarian)** — `admin/`
- Secure login/logout using PHP sessions and `password_hash()` / `password_verify()`
- Full CRUD on books: title, author, ISBN, genre, total/available copies
- Cover image upload (JPG/PNG/GIF, max 2MB) with type & size validation
- Dashboard with real-time inventory metrics (total titles, total copies, available copies, total reservations, out-of-stock list, recent activity)
- View reservations for each individual book, and a combined "All Reservations" view
- Search books by title/author/ISBN

**Public Portal (Students)** — `index.php`, `reserve.php`
- Browse/search/filter available books by title, author, or genre
- Reserve a book by entering name, telephone, required date, and return date
- Server-side validation: required fields, valid phone format, return date after required date, no past dates
- Prevents over-booking with a DB transaction + row lock, and blocks reservation once a book hits 0 copies
- Success/error feedback messages (Bootstrap alerts) after every action

## Database structure

3 normalized tables (see `sql/schema.sql`):
- `admins` — librarian accounts
- `books` — book inventory
- `reservations` — one row per reservation, with `book_id` as a foreign key to `books` (ON DELETE CASCADE)

## Setup (XAMPP)

1. Start Apache and MySQL from the XAMPP control panel.
2. Open **phpMyAdmin** (`http://localhost/phpmyadmin`), create a database, then import `sql/schema.sql`
   (or run: `mysql -u root -p < sql/schema.sql`). This also creates the default admin account and 3 sample books.
3. Make sure the `uploads/` folder is writable (it already exists in this package).
4. Visit `http://localhost/library_system/` for the public site,
   or `http://localhost/library_system/admin/login.php` for the librarian login.

**Default admin login:** `admin` / `admin123`

> If your MySQL uses a different username/password, edit `config/db.php`.

## Folder structure

```
library_system/
├── admin/              # Librarian portal (protected by session login)
│   ├── login.php / logout.php / auth_check.php
│   ├── dashboard.php   # metrics
│   ├── books.php       # list + search
│   ├── add_book.php / edit_book.php / delete_book.php
│   ├── reservations.php          # all reservations
│   └── book_reservations.php     # reservations for one book
├── config/db.php       # DB connection settings
├── includes/           # shared header/footer/navbar/helper functions
├── sql/schema.sql       # database schema + seed data
├── uploads/             # book cover images go here
├── index.php            # public book catalog
└── reserve.php          # public reservation form
```

## Notes

- All SQL queries use prepared statements (`mysqli::prepare`) to prevent SQL injection.
- Passwords are hashed with bcrypt (`password_hash`), never stored in plain text.
- `reserve.php` uses a database transaction with `SELECT ... FOR UPDATE` so two students can't
  simultaneously reserve the last copy of a book (a simple example of business logic / data integrity).
