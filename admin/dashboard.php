<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$success = isset($_GET['success']) ? $_GET['success'] : '';

$query = "
SELECT b.*, 
       u.name AS uname,
       u.email AS uemail,
       t.slot_label,
       t.slot_time
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN timeslots t ON b.timeslot_id = t.id
ORDER BY b.created_at DESC
";

$res = $mysqli->query($query);

$bookings = [];

if ($res) {
    $bookings = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
body{
background:url("images/bg4.jpg") no-repeat center center fixed;
background-size:cover;
}

.card{
background:rgba(255,255,255,0.8);
backdrop-filter:blur(8px);
}
</style>

</head>

<body class="min-h-screen p-6">

<div class="flex justify-between items-center mb-6">

<a href="../public/home.php" class="text-indigo-600 hover:underline">
← Back to Home
</a>

<h1 class="text-2xl font-bold text-gray-800">
Admin Dashboard
</h1>

<div class="space-x-2">

<a href="timeslots.php"
class="bg-indigo-600 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-700">

Manage Time Slots

</a>

<a href="../public/logout.php"
class="bg-gray-200 px-4 py-2 rounded-lg shadow hover:bg-gray-300">

Logout

</a>

</div>
</div>


<?php if(!empty($success)): ?>

<div class="card border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-center">

<?php echo htmlspecialchars($success); ?>

</div>

<?php endif; ?>


<?php if(empty($bookings)): ?>

<div class="card p-6 rounded-2xl shadow-lg text-center text-gray-600">

No bookings yet

</div>

<?php else: ?>

<div class="card rounded-2xl shadow-xl overflow-hidden">

<table class="w-full border-collapse">

<thead class="bg-indigo-50">

<tr>

<th class="px-6 py-3 text-left text-sm font-semibold">Date</th>

<th class="px-6 py-3 text-left text-sm font-semibold">Slot</th>

<th class="px-6 py-3 text-left text-sm font-semibold">User</th>

<th class="px-6 py-3 text-left text-sm font-semibold">Email</th>

<th class="px-6 py-3 text-left text-sm font-semibold">Status</th>

<th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>

</tr>

</thead>

<tbody class="divide-y divide-gray-200">

<?php foreach($bookings as $b): ?>

<?php
$status = isset($b['status']) ? strtolower($b['status']) : 'pending';

$color =
$status == 'accepted' ? 'text-green-600' :
($status == 'rejected' ? 'text-red-600' : 'text-yellow-600');
?>

<tr class="hover:bg-white/60">

<td class="px-6 py-3">

<?php echo htmlspecialchars($b['booking_date']); ?>

</td>

<td class="px-6 py-3">

<?php echo htmlspecialchars($b['slot_label']." — ".$b['slot_time']); ?>

</td>

<td class="px-6 py-3">

<?php echo htmlspecialchars($b['uname']); ?>

</td>

<td class="px-6 py-3">

<?php echo htmlspecialchars($b['uemail']); ?>

</td>

<td class="px-6 py-3 font-semibold">

<span class="<?php echo $color; ?>">

<?php echo ucfirst($status); ?>

</span>

</td>

<td class="px-6 py-3 flex gap-2 justify-center">

<a href="accept_booking.php?id=<?php echo $b['id']; ?>"
class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">

Accept

</a>

<a href="reject_booking.php?id=<?php echo $b['id']; ?>"
class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">

Reject

</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<?php endif; ?>

</body>
</html>