<?php
// --- 1. การตั้งค่าการเชื่อมต่อฐานข้อมูล ---
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "login_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- 2. รับข้อมูลจากฟอร์ม ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    // --- 3. ตรวจสอบข้อมูล ---
    // เช็คว่ารหัสผ่านตรงกันหรือไม่
    if ($password !== $password_confirmation) {
        die("รหัสผ่านไม่ตรงกัน กรุณาลองใหม่อีกครั้ง");
    }

    // เช็คว่ามีชื่อผู้ใช้นี้ในระบบแล้วหรือยัง
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die("ชื่อผู้ใช้นี้มีคนใช้แล้ว กรุณาเลือกชื่ออื่น");
    }
    $stmt->close();

    // --- 4. เข้ารหัสผ่าน (สำคัญมาก) ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // --- 5. บันทึกข้อมูลลงฐานข้อมูล ---
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        echo "สมัครสมาชิกสำเร็จ! <a href='index.html'>กลับไปหน้าเข้าสู่ระบบ</a>";
    } else {
        echo "เกิดข้อผิดพลาดในการสมัคร: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>