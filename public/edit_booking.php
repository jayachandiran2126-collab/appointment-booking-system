<?php
// public/edit_booking.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_user();

$uid = $_SESSION['user_id'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// fetch booking and ensure it belongs to the logged-in user
$stmt = $mysqli->prepare('SELECT * FROM bookings WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->bind_param('ii', $id, $uid);
$stmt->execute();
$res = $stmt->get_result();
$booking = $res->fetch_assoc();

if (!$booking) {
    echo 'Booking not found.';
    exit;
}

// fetch visible timeslots
$times = array();
$tsRes = $mysqli->query("SELECT id, slot_label, slot_time FROM timeslots WHERE visible = 1 ORDER BY id");
while ($r = $tsRes->fetch_assoc()) {
    $times[] = $r;
}

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = isset($_POST['booking_date']) ? $_POST['booking_date'] : $booking['booking_date'];
    $timeslot = isset($_POST['timeslot']) ? (int)$_POST['timeslot'] : (int)$booking['timeslot_id'];
    $notes = isset($_POST['notes']) ? esc($_POST['notes']) : '';

    $stmt = $mysqli->prepare('UPDATE bookings SET booking_date = ?, timeslot_id = ?, notes = ? WHERE id = ? AND user_id = ?');
    $stmt->bind_param('siisi', $date, $timeslot, $notes, $id, $uid);

    if ($stmt->execute()) {
        header('Location: myappointments.php');
        exit;
    } else {
        $error = 'Update failed.';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Booking</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
  <div class="p-4"><a href="home.php" class="text-indigo-600">&larr; Back to Home</a></div>

  <main class="max-w-2xl mx-auto p-6 bg-white rounded shadow">
    <?php if (!empty($error)): ?>
      <div class="bg-red-100 border border-red-300 text-red-700 p-3 rounded mb-4">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <label class="block mb-2">Date
        <input type="date" name="booking_date" value="<?php echo htmlspecialchars($booking['booking_date']); ?>" class="w-full border p-2 rounded">
      </label>

      <label class="block mb-2">Time Slot</label>
      <div class="mb-4">
        <?php foreach ($times as $t): ?>
          <label class="block">
            <input type="radio" name="timeslot" value="<?php echo $t['id']; ?>" <?php echo ($t['id'] == $booking['timeslot_id']) ? 'checked' : ''; ?>>
            <?php echo htmlspecialchars($t['slot_label'] . ' — ' . $t['slot_time']); ?>
          </label>
        <?php endforeach; ?>
      </div>

      <label class="block mb-4">Notes
        <textarea name="notes" class="w-full border p-2 rounded"><?php echo htmlspecialchars($booking['notes']); ?></textarea>
      </label>

      <div class="flex justify-between">
        <a href="myappointments.php" class="px-4 py-2 border rounded">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
      </div>
    </form>
  </main>
</body>
</html>
