<?php
session_start();
require_once 'C:/xampp/htdocs/projet_web/config/config.php';
// inclure ton fichier config

require 'C:\xampp\htdocs\projet_web\app\PHPMailer-master\src\PHPMailer.php';
require 'C:\xampp\htdocs\projet_web\app\PHPMailer-master\src\SMTP.php';
require 'C:\xampp\htdocs\projet_web\app\PHPMailer-master\src\Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = config::getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Vérifier si email existe
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "This email does not exist.";
    } else {
        // Générer un code
        $code = rand(100000, 999999);

        // Mettre à jour le code et expiration
        $stmt = $conn->prepare("UPDATE users SET reset_code = ?WHERE email = ?");
        $stmt->execute([$code, $email]);

        // Envoyer le mail
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'meddhiaab@gmail.com';
            $mail->Password = 'opmf lsqo lhap rodf';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('meddhiaab@gmail.com', 'My Website');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Code';
            $mail->Body = "Your reset code is: <b>$code</b>. It expires in 10 minutes.";
            $mail->AltBody = "Your reset code is: $code. It expires in 10 minutes.";

            $mail->send();

            $_SESSION['reset_email'] = $email;
            header("Location: verify_code.php");
            exit();

        } catch (Exception $e) {
            $error = "Could not send email. Error: {$mail->ErrorInfo}";
        }
    }
}
?>

<form action="" method="POST">
    <h3>Forgot Password</h3>
    <input type="email" name="email" placeholder="Enter your email" required>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <button type="submit">Send code</button>
</form>
