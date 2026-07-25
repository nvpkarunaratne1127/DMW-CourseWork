<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($page_title) ? e($page_title) . ' - Campus Library' : 'Campus Library'; ?></title>
<!-- Bootstrap 5 CSS (CDN) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background-color: #f6f7fb; }
    .navbar-brand { font-weight: 600; }
    .book-cover { height: 220px; object-fit: cover; width: 100%; border-radius: 8px 8px 0 0; background:#e9ecef; }
    .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border-radius: 10px; }
    .stat-card { border-radius: 12px; color: #fff; }
    footer { padding: 20px 0; text-align: center; color: #888; font-size: 14px; }
</style>
</head>
<body>
