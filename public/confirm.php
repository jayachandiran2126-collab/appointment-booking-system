<?php
// public/confirm.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_user();

// booking id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// fetch booking details
$stmt = $mysqli->prepare('
    SELECT b.*, t.slot_label, t.slot_time 
    FROM bookings b 
    JOIN timeslots t ON b.timeslot_id = t.id 
    WHERE b.id = ? 
    LIMIT 1
');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$booking = $res->fetch_assoc();

if (!$booking) {
    echo 'Booking not found.';
    exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Confirmed — Appointments</title>
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
<body class="bg-gray-50 min-h-screen flex flex-col">
  <!-- Top nav -->
  <div class="p-4 bg-white shadow">
    <a href="home.php" class="text-indigo-600 hover:underline">&larr; Back to Home</a>
  </div>

  <main class="flex-1 flex items-center justify-center p-6">
    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-lg w-full text-center">
      <!-- Success Icon -->
      <div class="flex justify-center mb-4">
        <div class="bg-green-100 text-green-600 w-16 h-16 flex items-center justify-center rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
      </div>

      <h2 class="text-2xl font-bold text-gray-800 mb-2">Booking Confirmed!</h2>
      <p class="text-gray-600 mb-6">Thank you — your appointment has been successfully booked.</p>

      <!-- Booking Details Card -->
      <div class="bg-gray-50 border rounded-xl p-4 text-left space-y-2 mb-6">
        <div><span class="font-medium text-gray-700">📅 Date:</span> <?php echo htmlspecialchars($booking['booking_date']); ?></div>
        <div><span class="font-medium text-gray-700">⏰ Slot:</span> <?php echo htmlspecialchars($booking['slot_label'] . ' — ' . $booking['slot_time']); ?></div>
        <div><span class="font-medium text-gray-700">👤 Name:</span> <?php echo htmlspecialchars($booking['name']); ?></div>
        <?php if (!empty($booking['phone'])): ?>
          <div><span class="font-medium text-gray-700">📞 Phone:</span> <?php echo htmlspecialchars($booking['phone']); ?></div>
        <?php endif; ?>
        <?php if (!empty($booking['notes'])): ?>
          <div><span class="font-medium text-gray-700">📝 Notes:</span> <?php echo htmlspecialchars($booking['notes']); ?></div>
        <?php endif; ?>
      </div>

      <!-- Actions -->
      <div class="space-x-3">
        <a href="myappointments.php" class="inline-block px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">View My Appointments</a>
        <a href="booking.php" class="inline-block px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition">Book Another</a>
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
