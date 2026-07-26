<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];
$title = $author = $isbn = $genre = '';
$total_copies = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title  = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn   = trim($_POST['isbn'] ?? '');
    $genre  = trim($_POST['genre'] ?? '');
    $total_copies = (int)($_POST['total_copies'] ?? 0);

    // --- Validation ---
    if ($title === '')  $errors[] = 'Title is required.';
    if ($author === '') $errors[] = 'Author is required.';
    if ($isbn === '')   $errors[] = 'ISBN is required.';
    if ($genre === '')  $errors[] = 'Genre is required.';
    if ($total_copies < 1) $errors[] = 'Total copies must be at least 1.';

    // Check ISBN uniqueness
    if ($isbn !== '') {
        $stmt = $conn->prepare('SELECT id FROM books WHERE isbn = ?');
        $stmt->bind_param('s', $isbn);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $errors[] = 'A book with this ISBN already exists.';
        }
        $stmt->close();
    }

    // --- File upload handling (cover image) ---
    $coverFileName = null;
    if (!empty($_FILES['cover_image']['name'])) {
        $file = $_FILES['cover_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'There was an error uploading the cover image.';
        } elseif (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Cover image must be a JPG, PNG, or GIF file.';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'Cover image must be smaller than 2MB.';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $coverFileName = 'book_' . uniqid() . '.' . $ext;
            $destination = __DIR__ . '/../uploads/' . $coverFileName;
            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                $errors[] = 'Failed to save the uploaded image.';
                $coverFileName = null;
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            'INSERT INTO books (title, author, isbn, genre, total_copies, available_copies, cover_image)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('ssssiis', $title, $author, $isbn, $genre, $total_copies, $total_copies, $coverFileName);
        $stmt->execute();
        $stmt->close();

        set_flash('success', 'Book added successfully.');
        header('Location: books.php');
        exit;
    }
}

$page_title = 'Add Book';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="container" style="max-width:600px;">
    <h3 class="mb-3">Add New Book</h3>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger"><?php echo e($err); ?></div>
    <?php endforeach; ?>

    <div class="card p-4">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo e($title); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Author</label>
                <input type="text" name="author" class="form-control" value="<?php echo e($author); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" value="<?php echo e($isbn); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Genre</label>
                    <input type="text" name="genre" class="form-control" value="<?php echo e($genre); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Copies</label>
                <input type="number" name="total_copies" min="1" class="form-control" value="<?php echo (int)$total_copies; ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cover Image (JPG/PNG/GIF, max 2MB)</label>
                <input type="file" name="cover_image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Save Book</button>
            <a href="books.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>