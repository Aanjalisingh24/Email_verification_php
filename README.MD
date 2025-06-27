# Email Verification & Daily XKCD Subscription

This PHP project allows users to:
- Subscribe to daily XKCD comics by email
- Verify their email before starting the subscription
- Unsubscribe easily with a single click

---

##  Project Structure

| File | Description |
|------|-------------|
| `index.php` | Main form for email subscription |
| `functions.php` | Shared functions for email sending, logging, etc. |
| `cron.php` | Sends daily comic email to all verified subscribers |
| `unsubscribe.php` | Handles unsubscribe requests |
| `unsubscribe_verify.php` | Confirms and processes unsubscribe actions |
| `setup_cron.sh` | Shell script to help schedule cron job |
| `registered_emails.txt` | Stores verified email list |
| `composer.json` | Manages PHP dependencies via Composer |


##  Install Dependencies (Important)

>  The `vendor` folder is not uploaded to GitHub (as per best practices). You **must install it manually**.

If you downloaded or cloned this project, follow these steps:

1. **Make sure you have [Composer](https://getcomposer.org/) installed.**
   - To check:  
     ```bash
     composer --version
     ```
   - If not installed, follow: https://getcomposer.org/download/

2. **Run this command in the project directory to install required dependencies:**
   ```bash
   composer install



## Requirements Before Running

Make sure you have the following tools installed:

1. **XAMPP (or MAMP/LAMP/WAMP)** – to run PHP and MySQL locally

2. **Composer** – to install dependencies

---

## ⚙️ How to Start the Server (Very Important)

> Without starting Apache and phpMyAdmin, this project won't run properly.

###  Using XAMPP:

1. Open **XAMPP Control Panel**
2. Start:
   - **Apache** (for running PHP files)
   - **MySQL** (for accessing `phpMyAdmin`)
3. Then go to your browser and open:
