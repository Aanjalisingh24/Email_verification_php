<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

function generateVerificationCode() {
    return rand(100000, 999999);
}

function sendVerificationEmail($email, $code, $type = 'verification code') {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'aanjalisingh.2536@gmail.com';
        $mail->Password = 'phhf rxnn efhm evqy'; //Gmail-password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('aanjalisingh.2536@gmail.com', 'XKCD Mailer');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = "<p>Your verification code is: <strong>$code</strong></p>";


        return $mail->send();
    } catch (Exception $e) {
        error_log('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

function registerEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    $email = strtolower(trim($email));

    $existing = file_exists($file) ? array_map('trim', file($file)) : [];

    if (!in_array($email, $existing)) {
       file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function unsubscribeEmail($email) {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return;
    $lines = file($file, FILE_IGNORE_NEW_LINES);
    $updated = array_filter($lines, fn($line) => trim($line) !== trim($email));
    file_put_contents($file, implode(PHP_EOL, $updated));
}


function verifyCode($email, $code, $type = 'subscribe') {
    $key = 'verification_' . $type . '_' . strtolower(trim($email));

    return isset($_SESSION[$key]) && strval($_SESSION[$key]) === strval($code);
}

