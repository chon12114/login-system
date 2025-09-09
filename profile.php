<?php
session_start();

// 1. ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง (โค้ดเหมือนใน welcome.php)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: index.html");
    exit;
}

// 2. เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "login_db";
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 3. ดึงข้อมูลผู้ใช้จากฐานข้อมูล โดยใช้ username จาก session
$stmt = $conn->prepare("SELECT id, username, role, created_at FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();

// หากไม่พบข้อมูล (เผื่อไว้)
if ($user === null) {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>โปรไฟล์ของฉัน</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>โปรไฟล์ของฉัน</h2>
        <div style="text-align: left; line-height: 2;">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
            <p><strong>ชื่อผู้ใช้:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>สิทธิ์ผู้ใช้:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
            <p><strong>วันที่สมัคร:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>

           <br> <a href="edit_profile.php" style="background-color: #28a745; color: white; padding: 10px; border-radius: 5px; text-decoration: none;">แก้ไขโปรไฟล์</a>
        </div>
        <br>
        <a href="welcome.php">กลับไปหน้าต้อนรับ</a>
        <a href="logout.php" style="margin-left: 10px;">ออกจากระบบ</a>
    </div>
    <script>
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === 'updated') {
        document.getElementById('success-message').textContent = 'อัปเดตข้อมูลโปรไฟล์เรียบร้อยแล้ว!';
    }
</script>
</body>
</html>