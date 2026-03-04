<?php
// public/booking.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_user();

$uid = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
$errors = [];

// fetch visible timeslots
$times = array();
$tsRes = $mysqli->query("SELECT id, slot_label, slot_time FROM timeslots ORDER BY id");
if ($tsRes) {
    while ($r = $tsRes->fetch_assoc()) {
        $times[] = $r;
    }
}

// POST: on final confirm insert booking and redirect to confirm.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? esc($_POST['name']) : '';
    $email = isset($_POST['email']) ? esc($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? esc($_POST['phone']) : '';
    $date = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';
    $timeslot = isset($_POST['timeslot']) ? (int)$_POST['timeslot'] : 0;
    $notes = isset($_POST['notes']) ? esc($_POST['notes']) : '';

    if (!$name || !$email || !$date || !$timeslot) {
        $errors[] = 'Please fill all required fields.';
    } else {
        $stmt = $mysqli->prepare('INSERT INTO bookings (user_id,name,email,phone,booking_date,timeslot_id,notes) VALUES (?,?,?,?,?,?,?)');
        if ($stmt) {
            $stmt->bind_param('isssiss', $uid, $name, $email, $phone, $date, $timeslot, $notes);
            if ($stmt->execute()) {
                $bid = $mysqli->insert_id;
                $stmt->close();
                header('Location: confirm.php?id=' . $bid);
                exit;
            } else {
                $errors[] = 'Could not save booking. Try again.';
            }
        } else {
            $errors[] = 'Database error: ' . $mysqli->error;
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Book — Appointments</title>
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
<body class="bg-gray-50 min-h-screen">

  <!-- Page header with Back button -->
  <header class="p-4">
    <a href="home.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition">
      <!-- home icon -->
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
      </svg>
      Back to Home
    </a>
  </header>
 
  <main class="max-w-3xl mx-auto p-6">
    <div class="bg-white/40 p-6 rounded-2xl shadow">
      <h2 class="text-xl font-semibold mb-4">Book Appointment</h2>

      <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 p-3 rounded mb-4 text-red-600">
          <?php echo htmlspecialchars(implode(' ', $errors)); ?>
        </div>
      <?php endif; ?>

      <form id="bookingForm" method="post" action="" autocomplete="off">
        <!-- Step 1: date & timeslot -->
        <div id="step1" class="step">
          <label class="block mb-3 text-sm font-medium text-gray-700">Select date
            <input id="booking_date" name="booking_date" type="date" class="w-full border p-2 rounded mt-1" required>
          </label>

          <label class="block mb-2 text-sm font-medium text-gray-700">Select time slot</label>
          <div class="grid grid-cols-1 gap-2 mb-4">
            <?php foreach ($times as $t): ?>
              <label class="p-3 border rounded flex items-center justify-between cursor-pointer">
                <span class="text-gray-800"><?php echo htmlspecialchars($t['slot_label'] . ' — ' . $t['slot_time']); ?></span>
                <input type="radio" name="timeslot" value="<?php echo (int)$t['id']; ?>">
              </label>
            <?php endforeach; ?>
          </div>

          <div class="flex justify-end">
            <button type="button" onclick="nextStep()" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition">Next</button>
          </div>
        </div>

        <!-- Step 2: personal details -->
        <div id="step2" class="step hidden">
          <label class="block mb-2 text-sm font-medium text-gray-700">Your name
            <input name="name" class="w-full border p-2 rounded mt-1" required autocomplete="name">
          </label>
          <label class="block mb-2 text-sm font-medium text-gray-700">Email
            <input name="email" type="email" class="w-full border p-2 rounded mt-1" required autocomplete="email">
          </label>
          <label class="block mb-2 text-sm font-medium text-gray-700">Phone
            <input name="phone" class="w-full border p-2 rounded mt-1" autocomplete="tel">
          </label>
          <label class="block mb-2 text-sm font-medium text-gray-700">Notes
            <textarea name="notes" class="w-full border p-2 rounded mt-1" rows="3" autocomplete="off"></textarea>
          </label>

          <div class="mt-6 flex justify-between">
            <button type="button" onclick="prevStep()" class="px-4 py-2 rounded border">Back</button>
            <button type="button" onclick="nextStep()" class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition">Next</button>
          </div>
        </div>

        <!-- Step 3: review & confirm -->
        <div id="step3" class="step hidden">
          <h3 class="font-semibold mb-2">Confirm details</h3>
          <div id="review" class="mb-4 text-sm text-gray-700"></div>

          <div class="flex justify-between">
            <button type="button" onclick="prevStep()" class="px-4 py-2 rounded border">Back</button>
            <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition">Confirm Booking</button>
          </div>
        </div>
      </form>
    </div>
  </main>

   <!-- ✅ Sticky Footer -->
  

  <script>
    (function () {
      const steps = Array.from(document.querySelectorAll('.step'));
      let current = 0;

      function show(i) {
        steps.forEach((s, idx) => s.classList.toggle('hidden', idx !== i));
        current = i;
      }

      window.nextStep = function () {
        if (current === 0) {
          const date = document.getElementById('booking_date').value;
          const timeslot = document.querySelector('input[name=timeslot]:checked');
          if (!date) { alert('Pick a date'); return; }
          if (!timeslot) { alert('Pick a time slot'); return; }
        }
        if (current === 1) {
          const name = document.querySelector('input[name=name]').value.trim();
          const email = document.querySelector('input[name=email]').value.trim();
          if (!name || !email) { alert('Enter your name and email'); return; }
        }
        if (current < steps.length - 1) show(current + 1);

        if (current === 2) {
          const date = document.getElementById('booking_date').value;
          const timeslotInput = document.querySelector('input[name=timeslot]:checked');
          const slotLabel = timeslotInput ? timeslotInput.parentElement.querySelector('span').textContent.trim() : '';
          const name = document.querySelector('input[name=name]').value.trim();
          const email = document.querySelector('input[name=email]').value.trim();
          const phone = document.querySelector('input[name=phone]').value.trim();
          const notes = document.querySelector('textarea[name=notes]').value.trim();

          document.getElementById('review').innerHTML =
            '<div class="py-2"><strong>Date:</strong> ' + escapeHtml(date) + '</div>' +
            '<div class="py-2"><strong>Slot:</strong> ' + escapeHtml(slotLabel) + '</div>' +
            '<div class="py-2"><strong>Name:</strong> ' + escapeHtml(name) + '</div>' +
            '<div class="py-2"><strong>Email:</strong> ' + escapeHtml(email) + '</div>' +
            '<div class="py-2"><strong>Phone:</strong> ' + escapeHtml(phone) + '</div>' +
            '<div class="py-2"><strong>Notes:</strong> ' + escapeHtml(notes) + '</div>';
        }
      };

      window.prevStep = function () {
        if (current > 0) show(current - 1);
      };

      function escapeHtml(str) {
        if (!str) return '';
        return String(str)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      show(0);
    })();
  </script>

  <body class="min-h-screen flex flex-col">

  <footer class="mt-auto">
    <div class="max-w-7xl mx-auto px-4 pb-4">
      <div class="bg-white/70 backdrop-blur-md rounded-xl shadow p-4 text-center text-gray-700 text-sm">
        © <?php echo date("Y"); ?> Appointment Booking System. All rights reserved.
      </div>
    </div>
  </footer>
</body>
</html>
    