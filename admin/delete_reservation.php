<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);

// Find the reservation first so we know which book to restore a copy to
$stmt = $conn->prepare('SELECT book_id FROM reservations WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$reservation = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reservation) {
    set_flash('danger', 'Reservation not found.');
    header('Location: reservations.php');
    exit;
}

$conn->begin_transaction();
try {
    // Delete the reservation
    $del = $conn->prepare('DELETE FROM reservations WHERE id = ?');
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();

    // Give the copy back to the book, but never exceed total_copies
    $upd = $conn->prepare(
        'UPDATE books
         SET available_copies = LEAST(total_copies, available_copies + 1)
         WHERE id = ?'
    );
    $upd->bind_param('i', $reservation['book_id']);
    $upd->execute();
    $upd->close();

    $conn->commit();
    set_flash('success', 'Reservation deleted and the copy has been made available again.');
} catch (Exception $e) {
    $conn->rollback();
    set_flash('danger', 'Something went wrong while deleting the reservation.');
}

// Send the librarian back to wherever they came from
$referer = $_SERVER['HTTP_REFERER'] ?? '';
if ($referer && str_contains($referer, 'book_reservations.php')) {
    header('Location: book_reservations.php?id=' . (int)$reservation['book_id']);
} else {
    header('Location: reservations.php');
}
exit;
