<?php
session_start();
include "db_con.php";

if (isset($_POST['login'])) {
    function validate($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    if (!isset($_SESSION['user_id']) || !isset($_SESSION['security_question_key']) || !isset($_SESSION['correct_answer'])) {
        header("Location: ../login.php?error=Session expired. Please login again.");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $answer_input = validate($_POST['answer']);
    $correct_answer = $_SESSION['correct_answer'];

    // Case-insensitive comparison
    if (strcasecmp($correct_answer, $answer_input) === 0) {
        // Security answer is correct

        // Get user data
        $sql_user = "SELECT * FROM users WHERE user_id = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("i", $user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();

        if ($user_data = $result_user->fetch_assoc()) {

            $role_sql = "SELECT role_id FROM user_roles WHERE user_id = ?";
            $role_stmt = mysqli_prepare($conn, $role_sql);
            mysqli_stmt_bind_param($role_stmt, "i", $user_id);
            mysqli_stmt_execute($role_stmt);
            $role_result = mysqli_stmt_get_result($role_stmt);
            if (mysqli_num_rows($role_result) === 1) {
                $role_row = mysqli_fetch_assoc($role_result);
                $role_id = $role_row['role_id'];
                $_SESSION['role_id'] = $role_id;

                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $user_data['username'];
                $_SESSION['firstname'] = $user_data['firstname'];
                $_SESSION['middlename'] = $user_data['middlename'];
                $_SESSION['lastname'] = $user_data['lastname'];
                $_SESSION['date_of_birth'] = $user_data['date_of_birth'];
                $_SESSION['gender'] = $user_data['gender'];
                $_SESSION['phone_number'] = $user_data['phone_number'];
                $_SESSION['email'] = $user_data['email'];
                $_SESSION['address'] = $user_data['address'];
                $_SESSION['enrolled_by'] = $user_data['enrolled_by'];
                $_SESSION['status'] = $user_data['status'];
                $_SESSION['profile_image'] = $user_data['profile_image'];
                $_SESSION['account_number'] = $user_data['account_number'];
                $_SESSION['login'] = true;

                $status = $user_data['status'];

                $updateStmt = $conn->prepare("UPDATE users SET  two_factor_authentication = 0 WHERE user_id = ?");
                $updateStmt->bind_param("s", $user_id);
                $updateStmt->execute();



                if ($status == "Banned") {
                    header("Location: ../login.php?accountBanned=This account was banned, please visit the front desk. Thank you");
                    exit;
                } else if ($status == "Suspended") {
                    header("Location: ../login.php?accountBanned=This account was suspended, please visit the front desk. Thank you");
                    exit;
                } else {
                    header("Location: ../dashboard.php");
                    exit;
                }
            } else {
                header("Location: ../login.php?error=Role not found.");
                exit();
            }


        } else {
            header("Location: ../security-question.php?error=User data not found.");
            exit();
        }
    } else {
        header("Location: ../security-question.php?error=Incorrect answer. Please try again.");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}