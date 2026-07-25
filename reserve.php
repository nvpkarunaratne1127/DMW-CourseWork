<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$book_id = (int)($_GET['book_id'] ?? $_POST['book_id'] ?? 0);

$stmt = $conn->prepare('SELECT * FROM books WHERE id = ?');
$stmt->bind_param('i', $book_id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$book) {
    set_flash('danger', 'Book not found.');
    header('Location: index.php');
    exit;
}

$errors = [];
$student_name = $telephone = '';
$request_date = date('Y-m-d');
$return_date = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $telephone    = trim($_POST['telephone'] ?? '');
    $request_date = trim($_POST['request_date'] ?? '');
    $return_date  = trim($_POST['return_date'] ?? '');

    // --- Validation ---
    if ($student_name === '') {
        $errors[] = 'Please enter your name.';
    }
    if ($telephone === '' || !preg_match('/^[0-9+\-\s]{7,15}$/', $telephone)) {
        $errors[] = 'Please enter a valid telephone number.';
    }
    if ($request_date === '' || $return_date === '') {
        $errors[] = 'Please select both a required date and a return date.';
    } else {
        $today = new DateTime('today');
        $req  = DateTime::createFromFormat('Y-m-d', $request_date);
        $ret  = DateTime::createFromFormat('Y-m-d', $return_date);

        if (!$req || !$ret) {
            $errors[] = 'Invalid date format.';
        } else {
            if ($req < $today) {
                $errors[] = 'The required date cannot be in the past.';
            }
            if ($ret <= $req) {
                $errors[] = 'The return date must be after the required date.';
            }
        }
    }

    // Re-check stock right before booking (business logic: prevent over-booking)
    if (empty($errors)) {
        $conn->begin_transaction();
        try {
            $lockStmt = $conn->prepare('SELECT available_copies FROM books WHERE id = ? FOR UPDATE');
            $lockStmt->bind_param('i', $book_id);
            $lockStmt->execute();
            $current = $lockStmt->get_result()->fetch_assoc();
            $lockStmt->close();

            if (!$current || $current['available_copies'] < 1) {
                $errors[] = 'Sorry, this book is no longer available.';
                $conn->rollback();
            } else {
                $ins = $conn->prepare(
                    'INSERT INTO reservations (book_id, student_name, telephone, request_date, return_date, status)
                     VALUES (?, ?, ?, ?, ?, "Reserved")'
                );
                $ins->bind_param('issss', $book_id, $student_name, $telephone, $request_date, $return_date);
                $ins->execute();
                $ins->close();

                $upd = $conn->prepare('UPDATE books SET available_copies = available_copies - 1 WHERE id = ?');
                $upd->bind_param('i', $book_id);
                $upd->execute();
                $upd->close();

                $conn->commit();

                set_flash('success', 'Reservation confirmed for "' . $book['title'] . '"! Please collect the book from the front desk.');
                header('Location: index.php');
                exit;
            }
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = 'Something went wrong. Please try again.';
        }
    }
}

$page_title = 'Reserve Book';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar_public.php';
?>

<div class="container" style="max-width:600px;">
    <h3 class="mb-3">Reserve: <?php echo e($book['title']); ?></h3>

    <div class="card p-3 mb-3 d-flex flex-row gap-3">
        <?php if ($book['cover_image']): ?>
            <img src="uploads/<?php echo e($book['cover_image']); ?>" style="width:80px;height:110px;object-fit:cover;border-radius:6px;">
        <?php endif; ?>
        <div>
            <p class="mb-1"><strong><?php echo e($book['title']); ?></strong></p>
            <p class="mb-1 text-muted">by <?php echo e($book['author']); ?></p>
            <span class="badge bg-success"><?php echo (int)$book['available_copies']; ?> copies available</span>
        </div>
    </div>

    <?php foreach ($errors as $err): ?>
        <div class="alert alert-danger"><?php echo e($err); ?></div>
    <?php endforeach; ?>

    <?php if ($book['available_copies'] < 1): ?>
        <div class="alert alert-warning">This book is currently out of stock and cannot be reserved.</div>
        <a href="index.php" class="btn btn-secondary">&larr; Back to catalog</a>
    <?php else: ?>
    <div class="card p-4">
        <form method="POST">
            <input type="hidden" name="book_id" value="<?php echo (int)$book_id; ?>">
            <div class="mb-3">
                <label class="form-label">Your Full Name</label>
                <input type="text" name="student_name" class="form-control" value="<?php echo e($student_name); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Telephone Number</label>
                <input type="text" name="telephone" class="form-control" placeholder="e.g. 0771234567" value="<?php echo e($telephone); ?>" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Required Date</label>
                    <input type="date" name="request_date" class="form-control" value="<?php echo e($request_date); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Return Date</label>
                    <input type="date" name="return_date" class="form-control" value="<?php echo e($return_date); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Confirm Reservation</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
