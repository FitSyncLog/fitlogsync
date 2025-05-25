<?php
require "db_con.php";



$plan_id = $_GET['plan_id'];

$query = "
    SELECT
        ph.date_time,
        ph.price,
        ph.status,
        u.lastname,
        u.firstname
    FROM
        plan_history ph
    JOIN
        users u ON ph.user_id = u.user_id
    WHERE
        ph.plan_id = ?
    ORDER BY
        ph.date_time DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $plan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . date('F d, Y | g:i A', strtotime($row['date_time'])) . "</td>";
        echo "<td>₱" . number_format($row['price'], 2) . "</td>";
        echo "<td>" . ($row['status'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-secondary">Deactivated</span>') . "</td>";
        echo "<td>" . htmlspecialchars($row['lastname'] . ', ' . $row['firstname']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No plan history found.</td></tr>";
}
?>
