<?php
include_once "../includes/connect_db.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/php/vendor/autoload.php';

function welcomeMail($pdo, $iduser) {
    try {
        $stmt1 = $pdo->prepare("SELECT f_name, email FROM user WHERE iduser=? ORDER BY iduser DESC LIMIT 1");
        $stmt1->execute([$iduser]);
        $user = $stmt1->fetch(PDO::FETCH_ASSOC);
    
    
        if ($user) {
            $mail = new PHPMailer(true);
    
            try {
                // SMTP settings (use your mail server or Gmail SMTP)
                $mail->isSMTP();                                            
                    $mail->Host       = 'smtp.hostinger.com';                     
                    $mail->SMTPAuth   = true;                                 
                    $mail->Username   = 'quarsi@miceff.com';                   
                    $mail->Password   = '@tt3nDanc3';                             
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
                    $mail->Port       = 465;                                    
    
                    $mail->setFrom('quarsi@miceff.com', 'Quarsi');
                    $mail->addAddress($user['email']);   
    
                    $mail->isHTML(true);                                  
                    $mail->Subject = 'Welcome to Quarsi';
                    $mail->Body    = "";
    
                $mail->Body = '
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body {
                            font-family: "Mulish", "Merriweather", sans-serif;
                            background-color: #ffffff;
                            color: #333333;
                            padding: 20px;
                        }
                        .container {
                            max-width: 600px;
                            margin: auto;
                            background: #fff;
                            border: 1px solid #eaeaea;
                            border-radius: 10px;
                            padding: 30px;
                            text-align: center;
                            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                        }
                        h1 {
                            color: #00995c;
                            font-size: 24px;
                            margin-bottom: 20px;
                        }
                        p {
                            font-size: 16px;
                            line-height: 1.5;
                            color: #555;
                        }
                        .btn {
                            display: inline-block;
                            margin-top: 20px;
                            padding: 12px 24px;
                            background-color: #00995c;
                            text-decoration: none;
                            font-size: 16px;
                            border-radius: 6px;
                            transition: background 0.3s ease;
                        }
                        .btn:hover {
                            background-color: #007a49;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1>Welcome to Quarsi, ' . htmlspecialchars($user['f_name']) . '!</h1>
                        <p>We’re excited to have you onboard. Quarsi is the official QR attendance system of the Association of Computer Scientists. Please go to the website and generate your QR.</p>
                        <a href="https://quarsi.miceff.com" class="btn" style="color: #fff; text-decoration: none; display: inline-block;">Visit Quarsi</a>
                    </div>
                </body>
                </html>
                ';
    
                // Send mail
                $mail->send();
                echo "Welcome email sent successfully to " . $user['email'];
    
            } catch (Exception $e) {
                echo "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            echo "User not found.";
        }
    } catch (PDOException $e) {
        echo "DB Connection failed: " . $e->getMessage();
    }

}
