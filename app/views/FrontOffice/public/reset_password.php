<?php
session_start();
require_once 'C:/xampp/htdocs/projet_web/config/config.php';
$conn = config::getConnection();

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['code_verified'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_code = NULL WHERE email = ?");
    $stmt->execute([$password, $email]);

    unset($_SESSION['reset_email']);
    unset($_SESSION['code_verified']);

    echo "<p>Password changed successfully!</p>";
    header("Location: login.php");
}
?>

<form action="" method="POST">
    <h3>New Password</h3>
    <input type="password" name="password" placeholder="Enter new password" required>
    <button type="submit">Change Password</button>
</form>
