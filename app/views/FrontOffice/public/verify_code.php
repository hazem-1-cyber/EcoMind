<?php
session_start();
require_once 'C:/xampp/htdocs/projet_web/config/config.php';

$conn = config::getConnection();

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND reset_code = ? ");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Invalid or expired code.";
    } else {
        $_SESSION['code_verified'] = true;
        header("Location: reset_password.php");
        exit();
    }
}
?>

<form action="" method="POST">
    <h3>Verify Code</h3>
    <input type="text" name="code" placeholder="Enter verification code" required>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <button type="submit">Verify</button>
</form>
