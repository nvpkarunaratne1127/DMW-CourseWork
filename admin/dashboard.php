<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// --- Real-time inventory metrics ---
$totalBooks     = $conn->query('SELECT COUNT(*) AS c FROM books')->fetch_assoc()['c'];
$totalCopies    = $conn->query('SELECT COALESCE(SUM(total_copies),0) AS c FROM books')->fetch_assoc()['c'];
$availableCopies = $conn->query('SELECT COALESCE(SUM(available_copies),0) AS c FROM books')->fetch_assoc()['c'];
$borrowedCopies = $totalCopies - $availableCopies;
$totalReservations = $conn->query('SELECT COUNT(*) AS c FROM reservations')->fetch_assoc()['c'];

// 5 most recent reservations
$recent = $conn->query(
    'SELECT r.id, r.student_name, r.request_date, r.return_date, b.title
     FROM reservations r
     JOIN books b ON b.id = r.book_id
     ORDER BY r.created_at DESC LIMIT 5'
);

// Books running low on stock (0 available)
$lowStock = $conn->query('SELECT title, total_copies FROM books WHERE available_copies = 0');

$page_title = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="container">
    <?php show_flash(); ?>
    <h3 class="mb-4">Dashboard</h3>

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card p-3" style="background:#4361ee;">
                <div class="small">Total Book Titles</div>
                <div class="fs-2 fw-bold"><?php echo (int)$totalBooks; ?></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card p-3" style="background:#2a9d8f;">
                <div class="small">Total Copies</div>
                <div class="fs-2 fw-bold"><?php echo (int)$totalCopies; ?></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card p-3" style="background:#f4a261;">
                <div class="small">Available Now</div>
                <div class="fs-2 fw-bold"><?php echo (int)$availableCopies; ?></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card p-3" style="background:#e76f51;">
                <div class="small">Total Reservations</div>
                <div class="fs-2 fw-bold"><?php echo (int)$totalReservations; ?></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card p-3">
                <h5>Recent Reservations</h5>
                <?php if ($recent->num_rows === 0): ?>
                    <p class="text-muted">No reservations yet.</p>
                <?php else: ?>
                    <table class="table table-sm align-middle">
                        <thead><tr><th>Book</th><th>Student</th><th>From</th><th>To</th></tr></thead>
                        <tbody>
                        <?php while ($row = $recent->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo e($row['title']); ?></td>
                                <td><?php echo e($row['student_name']); ?></td>
                                <td><?php echo e($row['request_date']); ?></td>
                                <td><?php echo e($row['return_date']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <a href="reservations.php" class="btn btn-sm btn-outline-primary">View all reservations</a>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card p-3">
                <h5>Out of Stock</h5>
                <?php if ($lowStock->num_rows === 0): ?>
                    <p class="text-muted">All books currently have copies available. 🎉</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                    <?php while ($row = $lowStock->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <?php echo e($row['title']); ?>
                            <span class="badge bg-danger">0 / <?php echo (int)$row['total_copies']; ?></span>
                        </li>
                    <?php endwhile; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
