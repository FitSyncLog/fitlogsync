<?php
session_start();
include "../indexes/db_con.php";

if (isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin') {
    // Fetch total members
    $sql_members = "SELECT COUNT(*) AS total_members FROM user_roles WHERE role = 'Member'";
    $result_members = mysqli_query($conn, $sql_members);
    $row_members = mysqli_fetch_assoc($result_members);
    $total_members = $row_members['total_members'];

    // Fetch total instructors
    $sql_instructor = "SELECT COUNT(*) AS total_members FROM user_roles WHERE role = 'Instructor'";
    $result_instructor = mysqli_query($conn, $sql_instructor);
    $row_instructor = mysqli_fetch_assoc($result_instructor);
    $total_instructor = $row_instructor['total_members'];

    // Fetch active members today
    $current_date = date("Y-m-d");
    $sub_query = "SELECT user_id FROM subscription WHERE starting_date <= '$current_date' AND expiration_date >= '$current_date'";
    $sub_result = mysqli_query($conn, $sub_query);
    $activeMember = mysqli_num_rows($sub_result);

    // Fetch total visits today
    $today = date("Y-m-d");
    $query = "SELECT COUNT(*) AS total_in FROM attendance_log WHERE transaction_type = 'IN' AND DATE(transaction_time) = '$today'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $totalVisit = $row['total_in'];

    // Return data as JSON
    echo json_encode([
        'total_members' => $total_members,
        'total_instructor' => $total_instructor,
        'activeMember' => $activeMember,
        'totalVisit' => $totalVisit
    ]);
} else {
    echo json_encode(['error' => 'Unauthorized access']);
}
?>