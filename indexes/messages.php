<?php
session_start();

include "db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    date_default_timezone_set("Asia/Manila");
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    $date_and_time = date("Y-m-d H:i:s");
    $status = "Unread";


    if (empty($name)) {
        header("Location: ..?Failed=Your name is required.#contact");
        exit();
    } else if (empty($email)) {
        header("Location: ..?Failed=Your email is required.#contact");
        exit();
    } else if (empty($subject)) {
        header("Location: ..?Failed=Subject is required.#contact");
        exit();
    } else if (empty($message)) {
        header("Location: ..?Failed=Your message is required.#contact");
        exit();
    } else {

        $sql_new_message = "INSERT INTO messages (name, email, subject, message, date_and_time, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_new_message = mysqli_prepare($conn, $sql_new_message);
        mysqli_stmt_bind_param($stmt_new_message, "ssssss", $name, $email, $subject, $message, $date_and_time, $status);
        $result_new_message = mysqli_stmt_execute($stmt_new_message);

        if ($result_new_message) {
            header("Location: ..?Success=Your message has been send, please wait for our reply.#contact");
            exit();
        } else {
            header("Location: ..?Failed=Unexpected error.#contact");
            exit();
        }
    }

} else {
    header("Location: ..?Failed=Please write a message first.#contact");
    exit();
}
?>