<?php
session_start();
include "../../indexes/db_con.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require("PHPMailer/PHPMailer.php");
require("PHPMailer/SMTP.php");
require("PHPMailer/Exception.php");

function sendMail($email, $subject, $name, $dateSent, $message, $repliedMessage, $replied_by, $role)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fitlogsync.official@gmail.com';
        $mail->Password = 'tjen yako tlcc knwi';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('fitlogsync.official@gmail.com', 'FiT-LOGSYNC');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = $subject . ' | FiT-LOGSYNC';

        // Publicly Hosted Image URLs (Replace with actual URLs)
        $headerImageURL = "https://rmmccomsoc.org/fitlogsync-email-header.png";

        $mail->Body = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #fff;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    overflow: hidden;
                }
                h1, h3, h5, p {
                    color: #000000;
                    margin: 5px 0;
                }
                p {
                    color: #3d3d3d;
                    margin: 5px 0;
                }
                .header-img {
                    width: 100%;
                    display: block;
                    border-top-left-radius: 10px;
                    border-top-right-radius: 10px;
                }
                .otp-code {
                    color: #F6C23E;
                    font-size: 4em;
                    font-weight: bold;
                    text-shadow: 3px 3px 8px rgba(128, 128, 128, 0.2);
                    letter-spacing: 10px;
                    display: inline-block;
                    margin: 5px 0;
                }
                .play-button {
                    display: inline-block;
                    margin-top: 10px;
                }
                .play-button img {
                    width: 150px;
                }
                .message-container {
                    text-align: left;
                    margin-top: 20px;
                    padding: 20px;
                    background: #f9f9f9;
                    border-radius: 5px;
                    box-shadow: 0px 0px 5px rgba(0,0,0,0.1);
                }
                hr {
                    margin: 15px 0;
                }
                .signature {
                    margin-top: 10px;
                    font-weight: bold;
                }
                .role {
                    color: gray;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <img src='{$headerImageURL}' alt='FitLogSync Header' class='header-img'>
                <div class='message-container'>
                    <p><strong>Sent By:</strong> {$name}</p>
                    <p><strong>Sent on:</strong> {$dateSent}</p>
                    <p><strong>Message:</strong> {$message}</p>
                    <hr>
                    <p>{$repliedMessage}</p>
                    <p class='signature'>{$replied_by}</p>
                    <p class='role'>{$role}</p>
                </div>
            </div>
        </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'Super Admin') {
    if (isset($_POST['replyButton'])) {
        // Get the message ID and reply message from the POST data
        $id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;
        $repliedMessage = $_POST['reply_message'] ?? '';

        if (empty($repliedMessage)) {
            header("Location: ../manage-all-help-messages.php?Failed=Message must not be empty");
            exit();
        } else {
            $query = "SELECT name, email, subject, message, date_and_time FROM messages WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // Check if a record is found
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();

                // Assign values to variables
                $name = $row['name'];
                $email = $row['email'];
                $subject = $row['subject'];
                $message = $row['message'];
                $dateSent = date("F j, Y g:i A", strtotime($row['date_and_time']));
                $status = "Replied";
                date_default_timezone_set('Asia/Manila');
                $reply_date = date("Y-m-d H:i:s");
                $replied_by = $_SESSION['firstname'] . ' ' . $_SESSION['middlename'] . ' ' . $_SESSION['lastname'];
                $role = $_SESSION['role'];

                $updateSql = "UPDATE messages SET status = ?, message_reply = ?, reply_date = ?, replied_by = ? WHERE id = ?";
                $updateStmt = mysqli_prepare($conn, $updateSql);
                mysqli_stmt_bind_param($updateStmt, "ssssi", $status, $repliedMessage, $reply_date, $replied_by, $id);
                mysqli_stmt_execute($updateStmt);

                if (sendMail($email, $subject, $name, $dateSent, $message, $repliedMessage, $replied_by, $role)) {
                    header("Location: ../manage-all-help-messages.php?Success=Message replied successfully.");
                    exit();
                } else {
                    header("Location: ../manage-all-help-messages.php?Failed=Unexpected error occurred");
                    exit();
                }
            } else {
                header("Location: ../manage-all-help-messages.php?Failed=Message not found");
                exit();
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    header("Location: ../../login.php?LoginFirst=Please login first.");
    exit();
}

mysqli_close($conn);
?>
