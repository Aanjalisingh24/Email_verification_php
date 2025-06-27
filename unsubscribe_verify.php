<?php
session_start();
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $code = $_POST['code'] ?? '';
    $key = 'unsubscribe_code_' . strtolower($email);

    if (isset($_SESSION[$key]) && $_SESSION[$key] === $code) {
        unsubscribeEmail($email);
        unset($_SESSION[$key]);
        echo "<p style='color:green;'>You have been successfully unsubscribed.</p>";
    } else {
        echo "<p style='color:red;'>Invalid verification code.</p>";
    }
}
?>
