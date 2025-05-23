<?php
session_start();
include "db_con.php";

if (isset($_POST['upload'])) {

    $user_id = $_SESSION['user_id'];

    // Make sure to use the correct input name
    $file_name = $_FILES['profile_image']['name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_loc = $_FILES['profile_image']['tmp_name'];
    $file_size = $_FILES['profile_image']['size'];
    $folder = "../assets/profile-pictures/";
    $account_number = $_SESSION['account_number'];

    $allowed_extensions = array('png', 'jpg', 'jpeg');

    if (!in_array(strtolower($file_ext), $allowed_extensions)) {
        header("Location: ../change-profile-photo.php?Failed=Upload failed, file type is not supported. Please upload PNG, JPG, or JPEG file type only.");
        exit();
    }

    $final_file = strtolower($account_number) . '.' . $file_ext;

    if (move_uploaded_file($file_loc, $folder . $final_file)) {

        $sql = "UPDATE users SET profile_image=? WHERE user_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $final_file, $user_id);
        mysqli_stmt_execute($stmt);

        $_SESSION['profile_image'] = $final_file;

        header("Location: ../change-profile-photo.php?Success=Your new profile picture has been updated successfully.");
        exit();
    } else {
        header("Location: ../change-profile-photo.php?Failed=Upload failed.");
        exit();
    }

} else {
    header("Location: ../change-profile-photo.php");
    exit();
}
?>
