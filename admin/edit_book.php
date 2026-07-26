<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare('SELECT * FROM books WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$book) {
    set_flash('danger', 'Book not found.');
    header('Location: books.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title  = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isbn   = trim($_POST['isbn'] ?? '');
    $genre  = trim($_POST['genre'] ?? '');
    $total_copies = (int)($_POST['total_copies'] ?? 0);

    if ($title === '')  $errors[] = 'Title is required.';
    if ($author === '') $errors[] = 'Author is required.';
    if ($isbn === '')   $errors[] = 'ISBN is required.';
    if ($genre === '')  $errors[] = 'Genre is required.';
    if ($total_copies < 1) $errors[] = 'Total copies must be at least 1.';

    // ISBN unique except for this book
    if ($isbn !== '') {
        $chk = $conn->prepare('SELECT id FROM books WHERE isbn = ? AND id != ?');
        $chk->bind_param('si', $isbn, $id);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            $errors[] = 'Another book already uses this ISBN.';
        }
        $chk->close();
    }

    // Adjust available_copies to reflect any change in total_copies,
    // without allowing it to go below the number of copies already borrowed.
    $borrowed = $book['total_copies'] - $book['available_copies'];
    $new_available = max(0, $total_copies - $borrowed);

    $coverFileName = $book['cover_image'];
    if (!empty($_FILES['cover_image']['name'])) {
        $file = $_FILES['cover_image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024;

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'There was an error uploading the cover image.';
        } elseif (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Cover image must be a JPG, PNG, or GIF file.';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'Cover image must be smaller than 2MB.';
        } else {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'book_' . uniqid() . '.' . $ext;
            $destination = __DIR__ . '/../uploads/' . $newFileName;
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // remove old image if it exists
                if ($book['cover_image'] && file_exists(__DIR__ . '/../uploads/' . $book['cover_image'])) {
                    unlink(__DIR__ . '/../uploads/' . $book['cover_image']);
                }
                $coverFileName = $newFileName;
            } else {
                $errors[] = 'Failed to save the uploaded image.';
            }
        }
    }

    if (empty($errors)) {
        $upd = $conn->prepare(
            'UPDATE books SET title=?, author=?, isbn=?, genre=?, total_copies=?, available_copies=?, cover_image=? WHERE id=?'
        );
        $upd->bind_param('ssssiisi', $title, $author, $isbn, $genre, $total_copies, $new_available, $coverFileName, $id);
        $upd->execute();
        $upd->close();

        set_flash('success', 'Book updated successfully.');
        header('Location: books.php');
        exit;
    } else {
        // keep form values on screen if validation failed
        $book = array_merge($book, compact('title', 'author', 'isbn', 'genre', 'total_copies'));
    }
}

$page_title = 'Edit Book';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="container" style="max-width:600px;">
    <h3 class="mb-3">Edit Book</h3>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger"><?php echo e($err); ?></div>
    <?php endforeach; ?>

    <div class="card p-4">
        <?php if ($book['cover_image']): ?>
            <img src="../uploads/<?php echo e($book['cover_image']); ?>" style="width:100px;border-radius:6px;" class="mb-3">
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo e($book['title']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Author</label>
                <input type="text" name="author" class="form-control" value="<?php echo e($book['author']); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" value="<?php echo e($book['isbn']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Genre</label>
                    <input type="text" name="genre" class="form-control" value="<?php echo e($book['genre']); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Copies</label>
                <input type="number" name="total_copies" min="1" class="form-control" value="<?php echo (int)$book['total_copies']; ?>" required>
                <div class="form-text">Currently <?php echo (int)$book['available_copies']; ?> available.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Replace Cover Image (optional)</label>
                <input type="file" name="cover_image" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
            <a href="books.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>


