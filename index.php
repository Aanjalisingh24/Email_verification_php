<?php
require 'vendor/autoload.php'; 
require 'functions.php';      

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    if (isset($_POST['subscribe_email']) && !isset($_POST['verification_code'])) {
        $email = trim(strtolower($_POST['subscribe_email']));
        $_SESSION['email'] = $email;
        $code = generateVerificationCode();

      
        $key = 'verification_subscribe_' . $email;
        $_SESSION[$key] = $code;

        sendVerificationEmail($email, $code, 'subscribe');
        $_SESSION['verification_sent'] = true;

echo '
<style>
  .message {
      position: absolute;
      top: 30px;
      text-align: center;
      width: 100%;
      font-size: 16px;
      font-weight: 400;
      padding: 0 20px;
    }

    .message strong {
      color: #FFD700;
    }
</style>
<div class="message">
<p>A verification code has been sent to <strong> ' . htmlspecialchars($email) . '</strong>. Please check your inbox.</p>
</div>
';
}

    if (isset($_POST['verification_code']) && isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        if (verifyCode($email, $_POST['verification_code'], 'subscribe')) {
            registerEmail($email);
            
            unset($_SESSION['email']);
            unset($_SESSION['verification_subscribe_' . $email]);
            echo '
<style>
  .message {
      position: absolute;
      top: 30px;
      text-align: center;
      width: 100%;
      font-size: 16px;
      font-weight: 400;
      padding: 0 20px;
    }

    .message strong {
      color: #FFD700;
    }
</style>
<div class="message">
<p>Email verified and subscribed: <strong> '. htmlspecialchars($email). ' </strong>.</p>
</div>';
        } else {
            echo "<p>Invalid code. Please try again.</p>";
        }
    }
    if (isset($_SESSION['verification_sent'])) {
        unset($_SESSION['verification_sent']); 
    }

    // 3. Handle unsubscribe form submission
    elseif (isset($_POST['unsubscribe_email']) && filter_var($_POST['unsubscribe_email'], FILTER_VALIDATE_EMAIL)) {
        $email = strtolower(trim($_POST['unsubscribe_email']));
        $unsubscribeUrl = 'http://localhost/Email_verification_php/unsubscribe.php?email=' . urlencode($email);
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'aanjalisingh.2536@gmail.com';
    $mail->Password = 'phhf rxnn efhm evqy'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

  
    $mail->SMTPDebug = 0; 
    $mail->Debugoutput = '';

    $mail->setFrom('aanjalisingh.2536@gmail.com', 'XKCD Mailer');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Unsubscribe Request';
    $mail->Body = "<p>Click below to confirm unsubscription:</p><p><a href=\"$unsubscribeUrl\">Unsubscribe Now</a></p>";

    $mail->send();
    echo '
<style>
    .message {
      position: absolute;
      top: 35px;
      text-align: center;
      width: 100%;
      font-size: 16px;
      font-weight: 400;
      padding: 0 10px;
    }
    .message strong {
      color: #FFD700;
    }
</style>
<div class="message">
    <p>An unsubscribe link has been sent to <strong> ' .htmlspecialchars($email).'<strong></p>
</div>'
;
} catch (Exception $e) {
    echo "<p style='color:red;'>Error sending email: {$mail->ErrorInfo}</p>";
}
}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Subscription Page</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #2e2e2e;
      color: #ffffff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
       overflow: hidden;
    }

    .form-container {
      background-color: #3a3a3a;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
      width: 100%;
      max-width: 400px;
      margin-top: 30px;
    }

    .form-container h3 {
      text-align: center;
      margin-bottom: 10px;
      font-size: 22px;
      border-bottom: 1px solid #555;
      padding-bottom: 5px;
    }

    form {
      display: flex;
      flex-direction: column;
      margin-bottom: 25px;
    }

    input[type="email"],
    input[type="text"] {
      padding: 10px;
      margin-top: 10px;
      border: none;
      border-radius: 6px;
      background-color: #555;
      color: white;
      font-size: 14px;
    }

    input::placeholder {
      color: #ccc;
    }

    button {
      margin-top: 15px;
      padding: 10px;
      background-color: #FFD700;
      color: #000000;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background-color: #000000;
      color: #FFD700;
    }
  .page-heading {
  font-size: 32px;
  color: #FFD700;
  text-align:center;
  }
  .center-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

  </style>
</head>
<body>
 <h1 class="page-heading">Get Your Daily Comic – Subscribe Now!</h1>
 <div class="center-container">
  <div class="form-container">
    <h3>Subscribe</h3>
    <form method="post">
      <input type="email" name="subscribe_email" required placeholder="Enter your email to subscribe">
      <button type="submit">Subscribe</button>
    </form>

    <h3>Verify</h3>
    <form method="post">
      <input type="text" name="verification_code" maxlength="6" required placeholder="Enter verification code">
      <button type="submit">Verify</button>
    </form>

    <h3>Unsubscribe</h3>
    <form method="post">
      <input type="email" name="unsubscribe_email" required placeholder="Enter your email to unsubscribe">
      <button type="submit">Send Unsubscribe Link</button>
    </form>
  </div>
</div>
</body>
</html>
