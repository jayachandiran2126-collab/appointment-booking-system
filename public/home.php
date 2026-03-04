<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_user();

$name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Home — Appointments</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: url("images/bg3.jpg") no-repeat center center fixed;
      background-size: cover;
    }
    .card {
      background: rgba(255, 255, 255, 0.85); /* white with 85% opacity */
      backdrop-filter: blur(6px); /* soft blur effect */
    }
  </style>
</head>
<body class="min-h-screen bg-gray-100 flex flex-col">
  <!-- Header -->
  <header class="bg-white shadow p-4 flex items-center justify-between">
    <a href="home.php" class="text-sm font-medium text-indigo-600 flex items-center gap-1">
      🏠 Home
    </a>
    
    <div class="text-gray-700 font-medium">
      👋 Hi, <?php echo htmlspecialchars($name); ?>
    </div>
    
    <a href="logout.php" 
       class="bg-red-600 text-Black px-4 py-2 rounded-md shadow hover:bg-red-700 transition">
       Logout
    </a>
  </header>

  <!-- Main -->
  <main class="flex-1 p-6 flex items-center justify-center">
    <div class="w-full max-w-2xl bg-white p-8 rounded-2xl shadow text-center">
      <h2 class="text-2xl font-bold mb-4 text-indigo-700">Book an Appointment</h2>
      <p class="text-gray-600 mb-6">
        Click <strong>Book Now</strong> to start your booking, or <strong>View My Appointments</strong> to see your existing ones.
      </p>
      <div class="flex justify-center gap-4">
        <a href="booking.php" 
           class="px-6 py-3 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
           Book Now
        </a>
        <a href="myappointments.php" 
           class="px-6 py-3 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">
           View My Appointments
        </a>
      </div>
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
