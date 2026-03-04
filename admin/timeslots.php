<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

/* ADD SLOT */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['slot_label'], $_POST['slot_time'])) {

    $label = trim($_POST['slot_label']);
    $time  = trim($_POST['slot_time']);

    if ($label && $time) {

        $stmt = $mysqli->prepare("INSERT INTO timeslots (slot_label, slot_time) VALUES (?, ?)");

        if ($stmt) {
            $stmt->bind_param("ss", $label, $time);
            $stmt->execute();
            $stmt->close();
        }
    }
}

/* DELETE SLOT */
if (isset($_GET['del'])) {

    $id = (int)$_GET['del'];

    $stmt = $mysqli->prepare("DELETE FROM timeslots WHERE id=?");

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
}

/* FETCH SLOTS */
$slots = [];

$res = $mysqli->query("SELECT * FROM timeslots ORDER BY id");

if ($res) {
    $slots = $res->fetch_all(MYSQLI_ASSOC);
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Manage Time Slots</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
body{
background:url("images/bg4.jpg") no-repeat center center fixed;
background-size:cover;
}

.card{
background:rgba(255,255,255,0.85);
backdrop-filter:blur(8px);
-webkit-backdrop-filter:blur(8px);
}
</style>

</head>

<body class="min-h-screen flex items-center justify-center p-6 relative">

<a href="dashboard.php"
class="absolute top-4 left-6 text-sm text-indigo-600 hover:underline">

← Back to Dashboard

</a>

<div class="card w-full max-w-2xl rounded-2xl shadow-xl p-6">

<h1 class="text-2xl font-bold text-gray-800 text-center mb-6">

⏰ Manage Time Slots

</h1>


<form method="POST" class="flex gap-3 mb-6">

<input
name="slot_label"
placeholder="Slot Label (eg: Morning)"
required
class="border p-2 rounded-lg flex-1 focus:ring-2 focus:ring-indigo-400"
>

<input
name="slot_time"
placeholder="Time (eg: 10:00–11:00 AM)"
required
class="border p-2 rounded-lg flex-1 focus:ring-2 focus:ring-indigo-400"
>

<button
class="bg-green-600 text-white px-4 rounded-lg shadow hover:bg-green-700 transition">

Add Slot

</button>

</form>


<div class="divide-y divide-gray-200">

<?php if(empty($slots)): ?>

<div class="text-center text-gray-600 py-4">

No time slots added yet.

</div>

<?php else: ?>

<?php foreach($slots as $s): ?>

<div class="flex justify-between items-center py-3 hover:bg-white/50 rounded-lg px-2 transition">

<div class="font-medium text-gray-800">

<?= htmlspecialchars(isset($s['slot_label']) ? $s['slot_label'] : '') ?>
—
<?= htmlspecialchars(isset($s['slot_time']) ? $s['slot_time'] : '') ?>

</div>

<a
href="?del=<?= $s['id'] ?>"
class="text-red-600 hover:text-red-800 font-medium"
onclick="return confirm('Delete this slot?')">

Delete

</a>

</div>

<?php endforeach; ?>

<?php endif; ?>

</div>

</div>

</body>
</html>