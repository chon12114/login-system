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
        header("location: register.html?error=password_mismatch");
        exit(); // หยุดการทำงานทันที
    }

    // เช็คว่ามีชื่อผู้ใช้นี้ในระบบแล้วหรือยัง
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("location: register.html?error=username_taken");
        exit(); // หยุดการทำงานทันที
    }
    $stmt->close();

    // --- 4. เข้ารหัสผ่าน ---
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // --- 5. บันทึกข้อมูลลงฐานข้อมูล ---
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        // หากสำเร็จ ให้ส่งไปหน้าล็อกอินพร้อมข้อความแจ้งเตือน
        header("location: index.html?success=registration_complete");
        exit();
    } else {
        // หากเกิดข้อผิดพลาดที่ไม่คาดคิด
        header("location: register.html?error=database_error");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>