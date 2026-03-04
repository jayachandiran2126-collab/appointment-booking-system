<?php
// public/signup.php
require_once __DIR__ . '/../includes/db.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email     = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password  = isset($_POST['password']) ? $_POST['password'] : '';
    $password2 = isset($_POST['password2']) ? $_POST['password2'] : '';

    if ($name === '' || $email === '' || $password === '') {
        $errors[] = 'All fields are required.';
    } elseif ($password !== $password2) {
        $errors[] = 'Passwords do not match.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        if ($stmt === false) {
            $errors[] = 'Database prepare() failed: ' . $mysqli->error;
            error_log('Signup prepare failed: ' . $mysqli->error);
        } else {
            $stmt->bind_param('sss', $name, $email, $hash);
            if ($stmt->execute()) {
                $stmt->close();
                header('Location: index.php?registered=1');
                exit;
            } else {
                if ($mysqli->errno === 1062) {
                    $errors[] = 'That email is already registered.';
                } else {
                    $errors[] = 'Database insert failed: ' . $mysqli->error;
                    error_log('Signup execute failed: (' . $mysqli->errno . ') ' . $mysqli->error);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign Up — Appointment System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: url("images/bg.jpg") no-repeat center center fixed;
      background-size: cover;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50 relative">

  <!-- Admin Login -->
  <a href="../admin/login.php" class="absolute top-5 right-5 bg-indigo-600 text-white px-4 py-2 rounded-md shadow hover:bg-indigo-700 transition">Admin Login</a>
  <!-- Back Button -->
  <a href="index.php" class="absolute top-4 left-4 bg-indigo-600 text-white px-4 py-2 rounded-md shadow hover:bg-indigo-700 transition">← Back to Login</a>

  <main class="flex-1 flex items-center justify-center">
    <div class="w-full max-w-md bg-white/30 p-6 rounded-2xl shadow-md backdrop-blur-lg">
      <h2 class="text-2xl font-bold mb-4 text-center text-indigo-700">ᯓ➤ Create Account</h2>

      <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4">
          <?php foreach ($errors as $e) echo '<div>' . htmlspecialchars($e) . '</div>'; ?>
        </div>
      <?php endif; ?>

      <form method="post" action="" autocomplete="off">
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Full Name</label>
          <input type="text" name="name" class="w-full border p-2 rounded" required autocomplete="off">
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Email</label>
          <input type="email" name="email" class="w-full border p-2 rounded" required autocomplete="new-email">
        </div>
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Password</label>
          <input type="password" name="password" class="w-full border p-2 rounded" required autocomplete="new-password">
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Confirm Password</label>
          <input type="password" name="password2" class="w-full border p-2 rounded" required autocomplete="new-password">
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">Sign Up</button>
      </form>

      <p class="mt-4 text-sm text-center">
        Already registered? <a href="index.php" class="text-indigo-600 font-medium hover:underline">Login</a>
      </p>
    </div>
  </main>
   <!-- ✅ Sticky Footer -->
  <footer class="mt-auto">
    <div class="max-w-7xl mx-auto px-4 pb-4">
      <div class="bg-white/70 backdrop-blur-md rounded-xl shadow p-4 text-center text-gray-700 text-sm">
        © <?php echo date("Y"); ?> Appointment Booking System. All rights reserved.
      </div>
    </div>
  </footer>

</body>
</html>
