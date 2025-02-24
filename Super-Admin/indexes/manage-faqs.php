<?php
session_start();

include "../../indexes/db_con.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin') {

    if (isset($_POST['addFAQ'])) {
        $newQuestion = $_POST['newQuestion'] ?? '';
        $newAnswer = $_POST['newAnswer'] ?? '';

        $user_data = 'newQuestion=' . $newQuestion .
            '&newAnswer=' . $newAnswer;


        if (empty($newQuestion)) {
            header("Location: ../manage-faqs.php?Failed=New Question is required&$user_data");
            exit();
        } else if (empty($newAnswer)) {
            header("Location: ../manage-faqs.php?Failed=New Answer is required&$user_data");
            exit();
        } else {

            $sql_add_new_faq = "INSERT INTO faq (question, answer) VALUES (?, ?)";
            $stmt_add_new_faq = mysqli_prepare($conn, $sql_add_new_faq);
            mysqli_stmt_bind_param($stmt_add_new_faq, "ss", $newQuestion, $newAnswer);
            $result_add_new_faq = mysqli_stmt_execute($stmt_add_new_faq);

            if ($result_add_new_faq) {
                header("Location: ../manage-faqs.php?Success=New F.A.Q successfully added");
                exit();
            } else {
                header("Location: ../manage-faqs.php?Failed=Failed to add new F.A.Q");
                exit();
            }
        }
    } else if (isset($_POST['editFAQ'])) {
        $editID = $_POST['editID'] ?? '';
        $editQuestion = $_POST['editQuestion'] ?? '';
        $editAnswer = $_POST['editAnswer'] ?? '';


        if (empty($editQuestion)) {
            header("Location: ../manage-faqs.php?Failed=Question is required");
            exit();
        } else if (empty($editAnswer)) {
            header("Location: ../manage-faqs.php?Failed=Answer is required");
            exit();
        }

        $sql_edit_faq = "UPDATE faq SET question=?, answer=? WHERE id=?";
        $stmt_edit_faq = mysqli_prepare($conn, $sql_edit_faq);
        mysqli_stmt_bind_param($stmt_edit_faq, "ssi", $editQuestion, $editAnswer, $editID);
        $result_edit_faq = mysqli_stmt_execute($stmt_edit_faq);

        if ($result_edit_faq) {
            header("Location: ../manage-faqs.php?Success=Address successfully updated");
            exit();
        } else {
            header("Location: ../manage-faqs.php?Failed=Failed to update the address");
            exit();
        }
    } else if (isset($_POST['deleteFAQ'])) {

        $deleteID = $_POST['deleteID'] ?? '';
        $deleteQuestion = $_POST['deleteQuestion'] ?? '';
        $deleteAnswer = $_POST['deleteAnswer'] ?? '';



        $sql_delete_faq = "DELETE FROM faq WHERE id=?";
        $stmt_delete_faq = mysqli_prepare($conn, $sql_delete_faq);
        mysqli_stmt_bind_param($stmt_delete_faq, "i", $deleteID);
        $result_delete_faq = mysqli_stmt_execute($stmt_delete_faq);

        if ($result_delete_faq) {
            header("Location: ../manage-faqs.php?Success=F.A.Q successfully delete");
            exit();
        } else {
            header("Location: ../manage-faqs.php?Failed=Failed to delete");
            exit();
        }
    }

} else {
    header("Location: ../../login.php?LoginFirst=Please login first.");
    exit();
}
?>