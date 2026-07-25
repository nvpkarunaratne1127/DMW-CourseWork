<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare('SELECT cover_image FROM books WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($book) {
    $del = $conn->prepare('DELETE FROM books WHERE id = ?');
    $del->bind_param('i', $id);
    $del->execute();
    $del->close();

    if ($book['cover_image'] && file_exists(__DIR__ . '/../uploads/' . $book['cover_image'])) {
        unlink(__DIR__ . '/../uploads/' . $book['cover_image']);
    }
    set_flash('success', 'Book deleted.');
} else {
    set_flash('danger', 'Book not found.');
}

header('Location: books.php');
exit;
