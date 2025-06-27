<?php
require_once __DIR__ . '/functions.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

file_put_contents(__DIR__ . '/cron_debug.txt', date("Y-m-d H:i:s") . " - cron.php started\n", FILE_APPEND);


$emailFile = __DIR__ . '/registered_emails.txt';
$users = file_exists($emailFile) ? file($emailFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

if (empty($users)) {
    file_put_contents(__DIR__ . '/cron_debug.txt', "No registered users found.\n", FILE_APPEND);
    exit("No registered users found.\n");
}

// Get latest XKCD comic
$latestComicJson = file_get_contents('https://xkcd.com/info.0.json');
if (!$latestComicJson) {
    file_put_contents(__DIR__ . '/cron_debug.txt', "Failed to fetch latest XKCD comic.\n", FILE_APPEND);
    exit("Failed to fetch latest XKCD comic.\n");
}

$latestComicData = json_decode($latestComicJson, true);
$latestComicId = $latestComicData['num'] ?? 2800;

// Random comic
$randomId = rand(1, $latestComicId);
$comicJson = file_get_contents("https://xkcd.com/$randomId/info.0.json");
if (!$comicJson) {
    file_put_contents(__DIR__ . '/cron_debug.txt', "Failed to fetch XKCD comic #$randomId.\n", FILE_APPEND);
    exit("Failed to fetch XKCD comic #$randomId.\n");
}

$comicData = json_decode($comicJson, true);
$imageUrl = $comicData['img'] ?? '';
$comicTitle = $comicData['safe_title'] ?? 'XKCD Comic';

$subject = "Your XKCD Comic:";
$body = "<h2>XXCD Comic</h2>
<img src=\"$imageUrl\" alt=\"XKCD Comic\">
<p><a href='#'>Unsubscribe</a></p>";

foreach ($users as $email) {
    file_put_contents(__DIR__ . '/cron_debug.txt', "Trying to send to $email...\n", FILE_APPEND);
    sendEmail($email, $subject, $body);
}

file_put_contents(__DIR__ . '/cron_debug.txt', date("Y-m-d H:i:s") . " - cron.php finished\n", FILE_APPEND);


function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'aanjalisingh.2536@gmail.com'; 
        $mail->Password = 'phhf rxnn efhm evqy';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('aanjalisingh.2536@gmail.com', 'XKCD Mailer');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->addCustomHeader('List-Unsubscribe', '<mailto:unsubscribe@example.com>');

        if ($mail->send()) {
            file_put_contents(__DIR__ . '/cron_debug.txt', "Email sent to $to\n", FILE_APPEND);
        } else {
            file_put_contents(__DIR__ . '/email_errors.txt', "Failed to send email to $to\n", FILE_APPEND);
        }
    } catch (Exception $e) {
        file_put_contents(__DIR__ . '/email_errors.txt', "Mailer Exception: " . $mail->ErrorInfo . "\n", FILE_APPEND);
    }
}
