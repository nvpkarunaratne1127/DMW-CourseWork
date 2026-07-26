<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Simple search feature (innovation & UX)
$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $conn->prepare(
        'SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? ORDER BY id DESC'
    );
    $like = '%' . $search . '%';
    $stmt->bind_param('sss', $like, $like, $like);
    $stmt->execute();
    $books = $stmt->get_result();
} else {
    $books = $conn->query('SELECT * FROM books ORDER BY id DESC');
}

$page_title = 'Manage Books';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="container">
    <?php show_flash(); ?>
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="mb-0">Manage Books</h3>
        <a href="add_book.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Add New Book</a>
    </div>

    <form method="GET" class="mb-3 d-flex gap-2" style="max-width:400px;">
        <input type="text" name="q" class="form-control" placeholder="Search by title, author, ISBN..." value="<?php echo e($search); ?>">
        <button class="btn btn-outline-primary">Search</button>
    </form>

    <div class="card p-3">
        <div class="table-responsive">
        <table class="table align-middle">
            <thead>
            <tr>
                <th>Cover</th><th>Title</th><th>Author</th><th>ISBN</th><th>Genre</th>
                <th>Available / Total</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($books->num_rows === 0): ?>
                <tr><td colspan="7" class="text-center text-muted">No books found.</td></tr>
            <?php endif; ?>
            <?php while ($b = $books->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if ($b['cover_image']): ?>
                            <img src="../uploads/<?php echo e($b['cover_image']); ?>" style="width:45px;height:60px;object-fit:cover;border-radius:4px;">
                        <?php else: ?>
                            <span class="text-muted small">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo e($b['title']); ?></td>
                    <td><?php echo e($b['author']); ?></td>
                    <td><?php echo e($b['isbn']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo e($b['genre']); ?></span></td>
                    <td>
                        <span class="badge <?php echo $b['available_copies'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                            <?php echo (int)$b['available_copies']; ?> / <?php echo (int)$b['total_copies']; ?>
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <a href="book_reservations.php?id=<?php echo (int)$b['id']; ?>" class="btn btn-sm btn-outline-secondary" title="View Reservations"><i class="bi bi-eye"></i></a>
                        <a href="edit_book.php?id=<?php echo (int)$b['id']; ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="delete_book.php?id=<?php echo (int)$b['id']; ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this book? This cannot be undone.');"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>


