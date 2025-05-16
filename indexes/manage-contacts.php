<?php
session_start();

include "../../indexes/db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin') {

    if (isset($_POST['newAddress'])) {
        $address = $_POST['newAddress'] ?? '';

        if (empty($address)) {
            header("Location: ../manage-contacts.php?Failed=New Address is required");
            exit();
        }

        // address
        $sql_update_address = "UPDATE information SET description = ? WHERE information_for = 'address'";
        $stmt_update_address = mysqli_prepare($conn, $sql_update_address);
        mysqli_stmt_bind_param($stmt_update_address, "s", $address, );
        $result_update_address = mysqli_stmt_execute($stmt_update_address);

        if ($result_update_address) {
            header("Location: ../manage-contacts.php?Success=Address successfully updated");
            exit();
        } else {
            header("Location: ../manage-contacts.php?Failed=Failed to update the address");
            exit();
        }
    }

    if (isset($_POST['newPhoneNumber'])) {
        $phone_number = $_POST['newPhoneNumber'] ?? '';

        if (empty($phone_number)) {
            header("Location: ../manage-contacts.php?Failed=New phone number is required");
            exit();
        }

        // Phone Number
        $sql_update_phone_number = "UPDATE information SET description = ? WHERE information_for = 'phone_number'";
        $stmt_update_phone_number = mysqli_prepare($conn, $sql_update_phone_number);
        mysqli_stmt_bind_param($stmt_update_phone_number, "s", $phone_number, );
        $result_update_phone_number = mysqli_stmt_execute($stmt_update_phone_number);

        if ($result_update_phone_number) {
            header("Location: ../manage-contacts.php?Success=Phone number successfully updated");
            exit();
        } else {
            header("Location: ../manage-contacts.php?Failed=Failed to update the phone number");
            exit();
        }
    }

    if (isset($_POST['newTwitter'])) {
        $x = $_POST['newTwitter'] ?? '';

        if (empty($x)) {
            header("Location: ../manage-contacts.php?Failed=New X link is required");
            exit();
        }

        // X
        $sql_update_x = "UPDATE information SET description = ? WHERE information_for = 'x'";
        $stmt_update_x = mysqli_prepare($conn, $sql_update_x);
        mysqli_stmt_bind_param($stmt_update_x, "s", $x, );
        $result_update_x = mysqli_stmt_execute($stmt_update_x);

        if ($result_update_x) {
            header("Location: ../manage-contacts.php?Success=X account link successfully updated");
            exit();
        } else {
            header("Location: ../manage-contacts.php?Failed=Failed to update the X account link");
            exit();
        }
    }

    if (isset($_POST['newFacebook'])) {
        $facebook = $_POST['newFacebook'] ?? '';

        if (empty($facebook)) {
            header("Location: ../manage-contacts.php?Failed=New Facebook link is required");
            exit();
        }
        // facebook
        $sql_update_facebook = "UPDATE information SET description = ? WHERE information_for = 'facebook'";
        $stmt_update_facebook = mysqli_prepare($conn, $sql_update_facebook);
        mysqli_stmt_bind_param($stmt_update_facebook, "s", $facebook, );
        $result_update_facebook = mysqli_stmt_execute($stmt_update_facebook);

        if ($result_update_facebook) {
            header("Location: ../manage-contacts.php?Success=Facebook account link successfully updated");
            exit();
        } else {
            header("Location: ../manage-contacts.php?Failed=Failed to update the Facebook account link");
            exit();
        }
    }

    if (isset($_POST['newInstagram'])) {
        $instagram = $_POST['newInstagram'] ?? '';

        if (empty($instagram)) {
            header("Location: ../manage-contacts.php?Failed=New Instagram link is required");
            exit();
        }

        // instagram
        $sql_update_instagram = "UPDATE information SET description = ? WHERE information_for = 'instagram'";
        $stmt_update_instagram = mysqli_prepare($conn, $sql_update_instagram);
        mysqli_stmt_bind_param($stmt_update_instagram, "s", $instagram, );
        $result_update_instagram = mysqli_stmt_execute($stmt_update_instagram);

        if ($result_update_instagram) {
            header("Location: ../manage-contacts.php?Success=Instagram account link successfully updated");
            exit();
        } else {
            header("Location: ../manage-contacts.php?Failed=Failed to update the Instagram account link");
            exit();
        }
    }

    if (isset($_POST['newYoutube'])) {
        $youtube = $_POST['newYoutube'] ?? '';

        if (empty($youtube)) {
            header("Location: ../manage-contacts.php?Failed=New Youtube link is required");
            exit();
        }

        // youtube
        $sql_update_youtube = "UPDATE information SET description = ? WHERE information_for = 'youtube'";
        $stmt_update_youtube = mysqli_prepare($conn, $sql_update_youtube);
        mysqli_stmt_bind_param($stmt_update_youtube, "s", $youtube, );
        $result_update_youtube = mysqli_stmt_execute($stmt_update_youtube);

        if ($result_update_youtube) {
            header("Location: ../manage-contacts.php?Success=Youtube account link successfully updated");
            exit();
        } else {
            header("Location: ../manage-contacts.php?Failed=Failed to update the Youtube account link");
            exit();
        }
    }

    if (isset($_POST['newTiktok'])) {
        $tiktok = $_POST['newTiktok'] ?? '';

        if (empty($tiktok)) {
            header("Location: ../manage-contacts.php?Failed=New Tiktok link is required");
            exit();
        }

        // tiktok
        $sql_update_tiktok = "UPDATE information SET description = ? WHERE information_for = 'tiktok'";
        $stmt_update_tiktok = mysqli_prepare($conn, $sql_update_tiktok);
        mysqli_stmt_bind_param($stmt_update_tiktok, "s", $tiktok, );
        $result_update_tiktok = mysqli_stmt_execute($stmt_update_tiktok);

        if ($result_update_tiktok) {
            header("Location: ../manage-contacts.php?Success=Tiktok account link successfully updated");
            exit();
        } else {
            header("Location: ../manage-contacts.php?Failed=Failed to update the Tiktok account link");
            exit();
        }
    }

    if (isset($_POST['newEmail'])) {
        $email = $_POST['newEmail'] ?? '';

        if (empty($email)) {
            header("Location: ../manage-contacts.php?Failed=New Email link is required");
            exit();
        }

        // email
        $sql_update_email = "UPDATE information SET description = ? WHERE information_for = 'email'";
        $stmt_update_email = mysqli_prepare($conn, $sql_update_email);
        mysqli_stmt_bind_param($stmt_update_email, "s", $email, );
        $result_update_email = mysqli_stmt_execute($stmt_update_email);

        if ($result_update_email) {
            header("Location: ../manage-contacts.php?Success=Email account link successfully updated");
            exit();
        } else {
            header("Location: ../manage-contacts.php?Failed=Failed to update the email account link");
            exit();
        }
    }
} else {
    header("Location: ../../login.php?LoginFirst=Please login first.");
    exit();
}
?>