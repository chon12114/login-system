<?php
session_start();

// 1. ตรวจสอบว่าผู้ใช้ล็อกอินหรือยัง
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

// 3. ดึงข้อมูลผู้ใช้ปัจจุบันมาแสดงในฟอร์ม
$stmt = $conn->prepare("SELECT id, username FROM users WHERE username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขโปรไฟล์</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>แก้ไขโปรไฟล์</h2>
        <form action="update_profile.php" method="post">
            <div class="input-group">
                <label for="username">ชื่อผู้ใช้:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <hr style="margin: 1rem 0;">
            <p style="text-align: center;"><em>(กรอกเฉพาะส่วนที่ต้องการเปลี่ยนรหัสผ่าน)</em></p>
            <div class="input-group">
                <label for="new_password">รหัสผ่านใหม่:</label>
                <input type="password" id="new_password" name="new_password">
            </div>
            <div class="input-group">
                <label for="confirm_password">ยืนยันรหัสผ่านใหม่:</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            <button type="submit">บันทึกการเปลี่ยนแปลง</button>
        </form>
        <br>
        <a href="profile.php">ยกเลิกและกลับไปหน้าโปรไฟล์</a>
    </div>
</body>
</html>