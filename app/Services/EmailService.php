<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    public function sendMail($to, $subject, $body, $fromEmail = 'mytrycodetests@gmail.com', $fromName = 'epharma')
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mytrycodetests@gmail.com';
            $mail->Password = 'ymot-fidv-kfic-hiqf';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();

            return [
                'status' => 'success',
                'message' => 'Email sent successfully',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}",
            ];
        }
    }
}
