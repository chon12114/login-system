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

// 3. รับข้อมูลจากฟอร์ม
$new_username = $_POST['username'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];
$current_username = $_SESSION['username'];

// 4. เตรียมการอัปเดตข้อมูล
// ตรวจสอบว่าชื่อผู้ใช้ใหม่ซ้ำกับคนอื่นหรือไม่ (ยกเว้นตัวเอง)
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND username != ?");
$stmt->bind_param("ss", $new_username, $current_username);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    die("ชื่อผู้ใช้นี้มีคนอื่นใช้แล้ว กรุณาเลือกชื่อใหม่");
}
$stmt->close();

// 5. จัดการการเปลี่ยนรหัสผ่าน (ถ้ามีการกรอก)
if (!empty($new_password)) {
    if ($new_password !== $confirm_password) {
        die("รหัสผ่านใหม่และการยืนยันไม่ตรงกัน");
    }
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    // อัปเดตทั้ง username และ password
    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ? WHERE username = ?");
    $stmt->bind_param("sss", $new_username, $hashed_password, $current_username);
} else {
    // อัปเดตเฉพาะ username
    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE username = ?");
    $stmt->bind_param("ss", $new_username, $current_username);
}

// 6. ทำการอัปเดตและส่งกลับ
if ($stmt->execute()) {
    // อัปเดต session ให้เป็นชื่อใหม่ด้วย
    $_SESSION['username'] = $new_username;
    // ส่งกลับไปหน้าโปรไฟล์พร้อมข้อความแจ้งเตือน
    header("location: profile.php?success=updated");
} else {
    echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
}

$stmt->close();
$conn->close();
?>