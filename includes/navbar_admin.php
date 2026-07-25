<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="dashboard.php"><i class="bi bi-book-half"></i> Library Admin</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="books.php">Manage Books</a></li>
        <li class="nav-item"><a class="nav-link" href="reservations.php">Reservations</a></li>
        <li class="nav-item"><a class="nav-link" href="../index.php" target="_blank">View Public Site</a></li>
        <li class="nav-item"><a class="nav-link text-warning" href="logout.php">Logout (<?php echo e($_SESSION['admin_username'] ?? ''); ?>)</a></li>
      </ul>
    </div>
  </div>
</nav>
