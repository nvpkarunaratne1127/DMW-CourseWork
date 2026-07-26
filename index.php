<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// --- Search & genre filter (innovation & UX) ---
$search = trim($_GET['q'] ?? '');
$genreFilter = trim($_GET['genre'] ?? '');

$sql = 'SELECT * FROM books WHERE 1=1';
$params = [];
$types = '';

if ($search !== '') {
    $sql .= ' AND (title LIKE ? OR author LIKE ?)';
    $like = '%' . $search . '%';
    $params[] = $like; $params[] = $like;
    $types .= 'ss';
}
if ($genreFilter !== '') {
    $sql .= ' AND genre = ?';
    $params[] = $genreFilter;
    $types .= 's';
}
$sql .= ' ORDER BY title ASC';

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$books = $stmt->get_result();

// list of distinct genres for the filter dropdown
$genres = $conn->query('SELECT DISTINCT genre FROM books ORDER BY genre ASC');

$page_title = 'Browse Books';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar_public.php';
?>

<div class="container">
    <?php show_flash(); ?>

    <div class="p-4 mb-4 rounded" style="background:linear-gradient(135deg,#4361ee,#3a0ca3); color:#fff;">
        <h2 class="mb-1"><i class="bi bi-journal-bookmark"></i> Browse & Reserve Books</h2>
        <p class="mb-0">Where every story awaits — discover, reserve, and enjoy your next adventure</p>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-6">
            <input type="text" name="q" class="form-control" placeholder="Search by title or author..." value="<?php echo e($search); ?>">
        </div>
        <div class="col-md-4">
            <select name="genre" class="form-select">
                <option value="">All Genres</option>
                <?php while ($g = $genres->fetch_assoc()): ?>
                    <option value="<?php echo e($g['genre']); ?>" <?php echo $genreFilter === $g['genre'] ? 'selected' : ''; ?>>
                        <?php echo e($g['genre']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    <div class="row g-4">
        <?php if ($books->num_rows === 0): ?>
            <p class="text-muted">No books matched your search.</p>
        <?php endif; ?>

        <?php while ($b = $books->fetch_assoc()): ?>
            <div class="col-md-4 col-sm-6">
                <div class="card h-100">
                    <?php if ($b['cover_image']): ?>
                        <img src="uploads/<?php echo e($b['cover_image']); ?>" class="book-cover">
                    <?php else: ?>
                        <div class="book-cover d-flex align-items-center justify-content-center text-muted">
                            <i class="bi bi-book" style="font-size:2.5rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title mb-1"><?php echo e($b['title']); ?></h5>
                        <p class="text-muted mb-1">by <?php echo e($b['author']); ?></p>
                        <span class="badge bg-secondary mb-2"><?php echo e($b['genre']); ?></span>
                        <p class="mb-2">
                            <?php if ($b['available_copies'] > 0): ?>
                                <span class="badge bg-success"><?php echo (int)$b['available_copies']; ?> available</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Out of stock</span>
                            <?php endif; ?>
                        </p>
                        <?php if ($b['available_copies'] > 0): ?>
                            <a href="reserve.php?book_id=<?php echo (int)$b['id']; ?>" class="btn btn-primary btn-sm w-100">Reserve</a>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm w-100" disabled>Unavailable</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>


