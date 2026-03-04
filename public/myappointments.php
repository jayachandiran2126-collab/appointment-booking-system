<?php
// public/myappointments.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_user();

$uid = 0;
if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    $uid = (int) $_SESSION['user_id'];
}

$bookings = [];

$sql = '
  SELECT b.*, t.slot_label, t.slot_time 
  FROM bookings b 
  JOIN timeslots t ON b.timeslot_id = t.id 
  WHERE b.user_id = ? 
  ORDER BY b.created_at DESC
';

$stmt = $mysqli->prepare($sql);
if ($stmt) {
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res) {
        $bookings = $res->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
} else {
    error_log('myappointments prepare failed: ' . $mysqli->error);
    $bookings = [];
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>My Appointments</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body {
      background: url("images/bg3.jpg") no-repeat center center fixed;
      background-size: cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    main {
      flex: 1;
      padding-bottom: 80px; /* space for footer */
    }
    footer {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      z-index: 10;
    }
  </style>
</head>
<body class="bg-gray-50">

  <!-- Top Nav -->
  <div class="p-4 flex items-center justify-between bg-white shadow">
    <a href="home.php" class="text-indigo-600 hover:underline">&larr; Back to Home</a>
    <a href="booking.php" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">+ New Appointment</a>
  </div>

  <main class="max-w-4xl mx-auto p-6">
    <h2 class="text-xl font-semibold mb-4">My Appointments</h2>

    <?php if (empty($bookings)): ?>
      <div class="bg-white p-4 rounded shadow text-center text-gray-600">No appointments yet.</div>
    <?php else: ?>
      <div class="space-y-4">
        <?php foreach ($bookings as $b):
            $status = isset($b['status']) ? strtolower($b['status']) : 'pending';
            if ($status === 'accepted') {
                $border = 'border-green-500';
                $label  = '<span class="text-green-600 font-medium">✔ Accepted</span>';
            } elseif ($status === 'rejected') {
                $border = 'border-red-500';
                $label  = '<span class="text-red-600 font-medium">✖ Rejected</span>';
            } else {
                $border = 'border-yellow-500';
                $label  = '<span class="text-yellow-600 font-medium">⌛ Pending</span>';
            }
        ?>
          <div class="bg-white p-4 rounded shadow flex justify-between items-start border-l-4 <?php echo $border; ?>">
            <div>
              <div class="font-semibold text-gray-800">
                <?php echo htmlspecialchars($b['booking_date']); ?> — <?php echo htmlspecialchars($b['slot_label']); ?>
              </div>
              <div class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($b['slot_time']); ?></div>
              <div class="mt-2 text-sm">Status: <?php echo $label; ?></div>
            </div>

            <div class="space-y-2 text-right">
              <a href="edit_booking.php?id=<?php echo (int)$b['id']; ?>" class="inline-block px-3 py-1 border rounded hover:bg-gray-100">Edit</a>
              <a href="delete_booking.php?id=<?php echo (int)$b['id']; ?>" onclick="return confirm('Delete this appointment?')" class="inline-block px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Delete</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- ✅ Fixed Sticky Footer -->
  <footer>
    <div class="max-w-7xl mx-auto px-4 pb-4">
      <div class="bg-white/80 backdrop-blur-md rounded-t-xl shadow p-4 text-center text-gray-700 text-sm">
        © <?php echo date("Y"); ?> Appointment Booking System. All rights reserved.
      </div>
    </div>
  </footer>

</body>
</html>
