<?php
session_start();
include "../indexes/db_con.php";

if (isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin') {
    if (isset($_GET['id'])) {
        $messageId = intval($_GET['id']);

        // Update the status to 'Read'
        $query = "UPDATE messages SET status = 'Read' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $messageId);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update status']);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}

mysqli_close($conn);
?>