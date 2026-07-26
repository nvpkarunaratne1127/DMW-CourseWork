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

$stmt = $conn->prepare('SELECT * FROM reservations WHERE book_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $id);
$stmt->execute();
$reservations = $stmt->get_result();

$page_title = 'Reservations for ' . $book['title'];
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="container">
    <h3 class="mb-1">Reservations for "<?php echo e($book['title']); ?>"</h3>
    <p class="text-muted">by <?php echo e($book['author']); ?> &middot; <?php echo (int)$book['available_copies']; ?>/<?php echo (int)$book['total_copies']; ?> available</p>

    <div class="card p-3">
        <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Student</th><th>Phone</th><th>From</th><th>To</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if ($reservations->num_rows === 0): ?>
                <tr><td colspan="6" class="text-center text-muted">No reservations for this book yet.</td></tr>
            <?php endif; ?>
            <?php while ($r = $reservations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo e($r['student_name']); ?></td>
                    <td><?php echo e($r['telephone']); ?></td>
                    <td><?php echo e($r['request_date']); ?></td>
                    <td><?php echo e($r['return_date']); ?></td>
                    <td><span class="badge bg-info text-dark"><?php echo e($r['status']); ?></span></td>
                    <td>
                        <a href="delete_reservation.php?id=<?php echo (int)$r['id']; ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Delete this reservation? The book copy will become available again.');">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
    <a href="books.php" class="btn btn-secondary mt-3">&larr; Back to Books</a>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
