<?php
// public/index.php
require_once __DIR__ . '/../includes/db.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? esc($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        $errors[] = 'Both fields are required.';
    } else {
        $stmt = $mysqli->prepare("SELECT id, name, password FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header("Location: home.php");
                exit;
            } else {
                $errors[] = 'Invalid email or password.';
            }
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Login — Appointment System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: url("images/bg.jpg") no-repeat center center fixed;
      background-size: cover;
    }
    .card {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(6px);
    }
  </style>
</head>
<body class="min-h-screen flex flex-col bg-gray-50 relative">

  <!-- ✅ Admin Login Button -->
  <a href="../admin/login.php" 
     class="absolute top-5 right-5 bg-indigo-600 text-white px-4 py-2 rounded-md shadow hover:bg-indigo-700 transition">
    Admin Login
  </a>

  <!-- Main -->
  <main class="flex-1 flex items-center justify-center">
    <div class="w-full max-w-md bg-white/30 backdrop-blur-lg p-6 rounded-2xl shadow-lg border border-white/30">
      <h2 class="text-2xl font-bold mb-4 text-center text-indigo-700">ᯓ➤ User Login</h2>

      <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4">
          <?php foreach ($errors as $err) echo "<div>" . htmlspecialchars($err) . "</div>"; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($_GET['registered'])): ?>
        <div class="bg-green-100 border border-green-300 text-green-700 p-3 rounded mb-4">
          Registration successful. Please sign in.
        </div>
      <?php endif; ?>

      <form method="POST" action="" autocomplete="off">
        <div class="mb-3">
          <label class="block text-sm font-medium mb-1">Email</label>
          <input type="email" name="email" class="w-full border p-2 rounded" required autocomplete="off">
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium mb-1">Password</label>
          <input type="password" name="password" class="w-full border p-2 rounded" required autocomplete="new-password">
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition">
          Login
        </button>
      </form>

      <p class="mt-4 text-sm text-center">
        Don’t have an account? 
        <a href="signup.php" class="text-indigo-600 font-medium hover:underline">Sign up</a>
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
