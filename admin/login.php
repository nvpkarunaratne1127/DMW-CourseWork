<?php
session_start();
$basePath = __DIR__ . '/../';

require_once $basePath . 'config/db.php';
require_once $basePath . 'includes/functions.php';

// If already logged in, go straight to the dashboard
//when running use php -S localhost:8001
if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = 'Please enter both username and password.';
    } else {
        // Prepared statement -> prevents SQL injection
        $stmt = $conn->prepare('SELECT id, username, password FROM admins WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
        $stmt->close();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Invalid username or password.';
        }
    }
}

$page_title = 'Librarian Login';
require_once $basePath. 'includes/header.php';
?>

<div class="container" style="max-width:420px; margin-top:60px;">
    <div class="card p-4">
        <h4 class="mb-3 text-center"></i> Librarian Login</h4>

        <?php foreach ($errors as $err): ?>
            <div class="alert alert-danger"><?php echo e($err); ?></div>
        <?php endforeach; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
                <p class="text-muted small mt-3 text-center">Default: admin </p>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
                <p class="text-muted small mt-3 text-center">Default:admin123</p>
            </div>
            <button type="submit" class="btn btn-primary w-100 login-btn">Login</button>
                <style>
                    .login-btn:hover {
                    background-color: green;   
                    border-color: green;      
                    }
                </style>
        </form>
        <br>
        <a href="../index.php" class="btn btn-primary back-btn">Back to Public Page</a>
                <style>
                    .back-btn:hover { 
                        background-color: red;
                        border-color: red;      
                    }
                </style>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
