<?php
session_start();

include "db_con.php";

if (isset($_POST['login'])) {

    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $email = validate($_POST['email']);
    $password = validate($_POST['password']);

    $user_data = 'email=' . $email;

    // Select account based on email
    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $row['password'])) {
            $user_id = $row['user_id'];
            // Store user data in session

            $role_sql = "SELECT role FROM user_roles WHERE user_id = ?";
            $role_stmt = mysqli_prepare($conn, $role_sql);
            mysqli_stmt_bind_param($role_stmt, "i", $user_id);
            mysqli_stmt_execute($role_stmt);
            $role_result = mysqli_stmt_get_result($role_stmt);

            if (mysqli_num_rows($role_result) === 1) {
                $role_row = mysqli_fetch_assoc($role_result);
                $role = $role_row['role'];
                $_SESSION['role'] = $role;

                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $row['username'];
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['middlename'] = $row['middlename'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['date_of_birth'] = $row['date_of_birth'];
                $_SESSION['gender'] = $row['gender'];
                $_SESSION['phone_number'] = $row['phone_number'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['address'] = $row['address'];
                $_SESSION['enrolled_by'] = $row['enrolled_by'];
                $_SESSION['status'] = $row['status'];
                $_SESSION['profile_image'] = $row['profile_image'];

                $status = $row['status'];

                // Redirect based on role
                switch ($role) {
                    case 'Member':

                        if ($status == "Banned") {
                            header("Location: ../login.php?accountBanned=This account was banned, please visit the front desk. Thank you");
                            exit;
                        } else if ($status == "Suspended") {
                            header("Location: ../login.php?accountBanned=This account was suspended, please visit the front desk. Thank you");
                            exit;
                        } else {
                            header("Location: ../Member/dashboard.php");
                            exit;
                        }
                    case 'Instructor':
                        header("Location: ../Instructor/dashboard.php");
                        exit;
                    case 'Front Desk':
                        header("Location: ../Front-Desk/dashboard.php");
                        exit;
                    case 'Admin':
                        header("Location: ../Admin/dashboard.php");
                        exit;
                    case 'Super Admin':
                        header("Location: ../Super-Admin/dashboard.php");
                        exit;
                    default:
                        // Handle unexpected roles
                        header("Location: ../login.php?UnexpectedRole=Role not recognized&$user_data");
                        exit;
                }

            } else {
                // Role not found
                header("Location: ../login.php?UnexpectedError=Unexpected Error, please try again later&$user_data");
                exit;
            }
        } else {
            // Incorrect password
            header("Location: ../login.php?incorrectPassword=Incorrect Username or Password&$user_data");
            exit;
        }

    } else {
        // Email not found
        header("Location: ../login.php?EmailisnotRegistered=Email Address is not found.&$user_data");
        exit;
    }

} else {
    // Redirect if login form is not submitted
    header("Location: ../login.php");
    exit();
}
?>