<?php
session_start();
include "../../indexes/db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin') {
    $youtubeUrl = $_POST['youtubeUrl'];




    // Convert YouTube URL to embed URL
    $embedUrl = preg_replace(
        "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
        "https://www.youtube.com/embed/$2",
        $youtubeUrl
    );

    if (empty($youtubeUrl)) {
        header("Location: ../manage-home.php?Failed=New Promotional Video link is required");
        exit();
    }

    // Update the database
    $query = "UPDATE information SET description = ? WHERE information_for = 'home_video'";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $embedUrl);
    mysqli_stmt_execute($stmt);

    header("Location: ../manage-home.php?Success=Successfully updated the promotional video."); 
    exit();
} else {
    header("Location: ../../login.php?LoginFirst=Please login first.");
    exit();
}
?>