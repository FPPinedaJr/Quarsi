<?php
session_start();
include_once "../includes/connect_db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../assets/php/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == 'verify_email') {
        $email = $_POST['email'];

        $stmt1 = $pdo->prepare('SELECT * from user where email=?');
        $stmt1->execute([$email]);

        if ($stmt1->rowCount() > 0) {
            echo 'exists';

            $mail = new PHPMailer(true);

            $char_1 = rand(0, 9);
            $char_2 = rand(0, 9);
            $char_3 = rand(0, 9);
            $char_4 = rand(0, 9);
            $otp = $char_1 . $char_2 . $char_3 . $char_4;
            $_SESSION['otp'] = $otp;

            try {
                $mail->isSMTP();                                            
                $mail->Host       = 'smtp.gmail.com';                     
                $mail->SMTPAuth   = true;                                 
                $mail->Username   = 'acsmailer2303@gmail.com';                   
                $mail->Password   = 'hlzesybpehgrmyed';                             
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
                $mail->Port       = 465;                                    

                $mail->setFrom('acsmailer2303@gmail.com', 'Mailer');
                $mail->addAddress($email);   

                $mail->isHTML(true);                                  
                $mail->Subject = 'Quarsi OTP Verification';
                $mail->Body    = "
                <!DOCTYPE html>
                <html>
                <head>
                    <link rel='preconnect' href='https://fonts.googleapis.com'>
                    <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
                    <link href='https://fonts.googleapis.com/css2?family=Cookie&family=Merriweather+Sans:ital,wght@0,300..800;1,300..800&family=Mulish:ital,wght@0,200..1000;1,200..1000&display=swap' rel='stylesheet'>
                    
                    <style>
                        body {
                            background-color: #f4f4f4;
                            padding: 20px;
                            font-family: 'Merriweather Sans', sans-serif;
                        }
                        .email-container {
                            max-width: 600px;
                            background: white;
                            padding: 20px;
                            border-radius: 10px;
                            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
                            margin: auto;
                            text-align: center;
                        }
                        .header {
                            color: #00995c;
                            font-size: 28px;
                            font-weight: 600;
                            font-family: 'Mulish', sans-serif;
                            border-bottom: 3px solid #00995c;
                            padding-bottom: 10px;
                        }
                        .content {
                            font-size: 16px;
                            padding: 20px;
                            color: #333;
                            background-color: #e9f7f0;
                            border-radius: 8px;
                            margin-top: 15px;
                        }
                        .otp-box {
                            display: inline-block;
                            background: #00995c;
                            color: white;
                            font-size: 24px;
                            font-weight: bold;
                            padding: 10px 20px;
                            border-radius: 6px;
                            letter-spacing: 3px;
                            margin: 15px 0;
                        }
                        .footer {
                            font-size: 12px;
                            color: #666;
                            margin-top: 15px;
                            font-style: italic;
                        }
                    </style>
                    </head>
                    <body>
                        <div class='email-container'>
                            <div class='header'>Quarsi</div>
                            <div class='content'>
                                <h3>Change Password OTP Verification</h3>
                                <p>Use the OTP below to verify your request:</p>
                                <div class='otp-box'>$otp</div>
                                <p>This code expires in <strong>5 minutes</strong>.</p>
                            </div>
                            <div class='footer'>If you did not request this, please ignore this email.</div>
                        </div>
                    </body>
                    </html>";

                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

        } else {
            echo 'Account does not exists.';
        }

    }
}
