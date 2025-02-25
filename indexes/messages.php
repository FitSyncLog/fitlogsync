<?php
session_start();

include "db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';


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

        $sql_new_message = "INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)";
        $stmt_new_message = mysqli_prepare($conn, $sql_new_message);
        mysqli_stmt_bind_param($stmt_new_message, "ssss", $name, $email, $subject, $message);
        $result_new_message = mysqli_stmt_execute($stmt_new_message);

        if ($result_new_message) {
            header("Location: ..?Success=Your message has been send, please wait for our replyj.#contact");
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