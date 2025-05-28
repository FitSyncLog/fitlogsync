<?php


// Then use the namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendWelcomeEmail($email, $name, $accountNumber)
{
    require("PHPMailer/PHPMailer.php");
    require("PHPMailer/SMTP.php");
    require("PHPMailer/Exception.php");

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fitlogsync.official@gmail.com';
        $mail->Password = 'tjen yako tlcc knwi';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('fitlogsync.official@gmail.com', 'FiT-LOGSYNC');
        $mail->addAddress($email);

        // Check front access card file
        $frontCardPath = __DIR__ . "/../assets/access-card/{$accountNumber}-accesscard.jpg";

        if (!file_exists($frontCardPath)) {
            throw new Exception("Access card file not found");
        }

        // Attachment (front card only)
        $mail->addAttachment($frontCardPath, "FiTLOGSYNC_AccessCard_Front.jpg");

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Welcome to FiT-LOGSYNC! 🎉";

        // Publicly Hosted Image URLs
        $headerImageURL = "https://rmmccomsoc.org/fitlogsync-email-header.png";
        $googlePlayImageURL = "https://rmmccomsoc.org/google-play-email.png";

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
                        line-height: 1.6;
                    }
                    .header-img {
                        width: 100%;
                        display: block;
                        border-top-left-radius: 10px;
                        border-top-right-radius: 10px;
                    }
                    .welcome-text {
                        color: #F6C23E;
                        font-size: 2em;
                        font-weight: bold;
                        margin: 20px 0;
                    }
                    .play-button {
                        display: inline-block;
                        margin-top: 10px;
                    }
                    .play-button img {
                        width: 150px;
                    }
                    .card-note {
                        background-color: #f8f9fa;
                        padding: 15px;
                        border-radius: 5px;
                        margin: 20px 0;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <img src='{$headerImageURL}' alt='FitLogSync Header' class='header-img'>
                    
                    <div class='welcome-text'>Welcome to FiT-LOGSYNC!</div>
                    
                    <p>Dear {$name},</p>
                    
                    <p>Welcome to FiT-LOGSYNC! We're thrilled to have you as a member of our fitness community. Your account has been successfully created and is ready to use.</p>

                    <div class='card-note'>
                        <p><strong>📱 Your E-Access Card</strong></p>
                        <p>We've attached your digital access card to this email.</p>
                        <p>Please save this card on your phone for easy access to the gym.</p>
                    </div>

                    <p>Download our mobile app to:</p>
                    <ul style='text-align: left; margin: 20px 40px;'>
                        <li>Track your attendance</li>
                        <li>Monitor your subscription</li>
                    </ul>

                    <a href='#' class='play-button'>
                        <img src='{$googlePlayImageURL}' alt='Get it on Google Play'>
                    </a>

                    <hr style='margin: 20px 0;'>
                    <p style='font-size: 0.9em; color: #666;'>This message was sent to {$email}</p>
                </div>
            </body>
        </html>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Failed to send welcome email: " . $e->getMessage());
        return false;
    }
}
?> 