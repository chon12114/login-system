<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ยินดีต้อนรับ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>คุณได้เข้าสู่ระบบเรียบร้อยแล้ว</p>
        <a href="logout.php">ออกจากระบบ</a>
    </div>
</body>
</html>