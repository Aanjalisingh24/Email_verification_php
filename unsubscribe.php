<?php
require 'functions.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unsubscribe_email']) && !isset($_POST['verification_code'])) {
    $email = strtolower(trim($_POST['unsubscribe_email']));

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $code = generateVerificationCode();
        $_SESSION['unsubscribe_email'] = $email;
        $_SESSION['unsubscribe_code_' . $email] = $code;

        sendVerificationEmail($email, $code, 'unsubscribe');
        
echo '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify & Unsubscribe</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #2e2e2e;
      color: white;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    .message {
      position: absolute;
      top: 10px;
      text-align: center;
      width: 100%;
      font-size: 16px;
      font-weight: 400;
      padding: 0 20px;
    }

    .message strong {
      color: #FFD700;
    }

    .verify-container {
      background-color: #3a3a3a;
      padding: 30px 35px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.4);
      width: 100%;
      max-width: 400px;
      margin-top: 80px; /* leave space below the message */
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

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
      margin-top: 20px;
      padding: 10px;
      background-color: #FFD700;
      color: #000;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    button:hover {
      background-color: #000;
      color: #FFD700;
    }
  </style>
</head>
<body>

<div class="message">
  A verification code has been sent to <strong>' . htmlspecialchars($email) . '</strong>. Please enter it below to unsubscribe.
</div>

  <div class="verify-container">
    <h2>Verify & Unsubscribe</h2>
    <form method="post">
      <input type="hidden" name="unsubscribe_email" value="' . htmlspecialchars($email) . '">
      <input type="text" name="verification_code" placeholder="Enter Verification Code" required>
      <button type="submit">Verify & Unsubscribe</button>
    </form>
  </div>

</body>
</html>
';
    exit;
    } else {
        echo "<p style='color:red;'>Invalid email format.</p>";
    }
}


$showForm = true;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'], $_POST['unsubscribe_email'])) {
    $email = strtolower(trim($_POST['unsubscribe_email']));
    $enteredCode = trim($_POST['verification_code']);
    $sessionKey = 'unsubscribe_code_' . $email;

   
    if (isset($_SESSION[$sessionKey]) && $_SESSION[$sessionKey] == $enteredCode) {
        unsubscribeEmail($email);
        unset($_SESSION[$sessionKey]);
        unset($_SESSION['unsubscribe_email']);
       echo "<p style='color:green;'>You have been successfully unsubscribed.</p>";
        $showForm = false; 
    } else {
        echo "<p style='color:red;'>Invalid code. Please try again.</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Unsubscribe</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #2e2e2e;
      color: #ffffff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .unsubscribe-container {
      background-color: #3a3a3a;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
      width: 100%;
      max-width: 400px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      font-size: 16px;
    }

    input[type="email"] {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: none;
      border-radius: 6px;
      background-color: #555;
      color: #fff;
      font-size: 14px;
    }

    input::placeholder {
      color: #ccc;
    }

    button {
      width: 100%;
      margin-top: 20px;
      padding: 10px;
      background-color: #FFD700;
      color: #000;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    button:hover {
      background-color: #000;
      color: #FFD700;
    }
  </style>
</head>
<body>

<?php if ($showForm): ?>
  <div class="unsubscribe-container">
    <h2>Unsubscribe</h2>
    <form method="post">
      <label for="unsubscribe_email">Enter your email to unsubscribe:</label>
      <input type="email" name="unsubscribe_email" id="unsubscribe_email" required placeholder="your@email.com">
      <button type="submit">Send Verification Code</button>
    </form>
  </div>
<?php endif; ?>

</body>
</html>
