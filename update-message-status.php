<?php
session_start();
include "../indexes/db_con.php";

if (isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin') {
    if (isset($_GET['id'])) {
        $messageId = intval($_GET['id']);

        // Check the current status of the message
        $checkQuery = "SELECT status FROM messages WHERE id = ?";
        $checkStmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($checkStmt, 'i', $messageId);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $currentStatus);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        if ($currentStatus === 'Unread') {
            // Update the status to 'Read'
            $updateQuery = "UPDATE messages SET status = 'Read' WHERE id = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, 'i', $messageId);
            mysqli_stmt_execute($updateStmt);

            if (mysqli_stmt_affected_rows($updateStmt) > 0) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update status']);
            }

            mysqli_stmt_close($updateStmt);
        } else {
            echo json_encode(['success' => false, 'error' => 'Status is not Unread']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid request']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
}

mysqli_close($conn);
?>
