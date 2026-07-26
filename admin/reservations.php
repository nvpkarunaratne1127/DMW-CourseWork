<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$reservations = $conn->query(
    'SELECT r.*, b.title AS book_title
     FROM reservations r
     JOIN books b ON b.id = r.book_id
     ORDER BY r.created_at DESC'
);

$page_title = 'All Reservations';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar_admin.php';
?>

<div class="container">
    <?php show_flash(); ?>
    <h3 class="mb-3">All Reservations</h3>
    <div class="card p-3">
        <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr><th>Book</th><th>Student</th><th>Phone</th><th>From</th><th>To</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if ($reservations->num_rows === 0): ?>
                <tr><td colspan="6" class="text-center text-muted">No reservations yet.</td></tr>
            <?php endif; ?>
            <?php while ($r = $reservations->fetch_assoc()): ?>
                <tr>
                    <td><?php echo e($r['book_title']); ?></td>
                    <td><?php echo e($r['student_name']); ?></td>
                    <td><?php echo e($r['telephone']); ?></td>
                    <td><?php echo e($r['request_date']); ?></td>
                    <td><?php echo e($r['return_date']); ?></td>
                    <td><span class="badge bg-info text-dark"><?php echo e($r['status']); ?></span></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>


