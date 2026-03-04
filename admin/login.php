<?php
require_once __DIR__ . '/../includes/db.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($username && $password) {
        $stmt = $mysqli->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($admin = $res->fetch_assoc()) {
            // Allow password_verify() or plain password check
            if (password_verify($password, $admin['password']) || $admin['password'] === $password) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['username'];
                header("Location: dashboard.php");
                exit;
            }
        }
        $errors[] = "Invalid credentials.";
    } else {
        $errors[] = "Please fill all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login — Appointment System</title>
  <script src="https://cdn.tailwindcss.com"></script>
   <style>
    body {
      background: url("images/bg2.jpg") no-repeat center center fixed;
      background-size: cover;
    }
    .card {
      background: rgba(255, 255, 255, 0.85); /* white with 85% opacity */
      backdrop-filter: blur(6px); /* soft blur effect */
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 relative">

  <!-- ✅ Back to User Login Button -->
  <a href="../public/index.php"
     class="absolute top-5 right-5 bg-indigo-600 text-white px-4 py-2 rounded-md shadow hover:bg-indigo-700 transition">
    ← Back to User Login
  </a>

  <div class="w-full max-w-md bg-white p-6 rounded-2xl shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-center text-indigo-700">ᯓ➤ Admin Login</h2>

    <?php if (!empty($errors)): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4">
        <?php echo implode('<br>', $errors); ?>
      </div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="mb-3">
        <label class="block text-sm font-medium mb-1">Username</label>
        <input type="text" name="username" class="w-full border p-2 rounded" required autocomplete="off">
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium mb-1">Password</label>
        <input type="password" name="password" class="w-full border p-2 rounded" required autocomplete="new-password">
      </div>

      <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">
        Login
      </button>
    </form>
  </div>

</body>
</html>
