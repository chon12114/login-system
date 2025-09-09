<?php
session_start();

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "login_db";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // ตรวจสอบรหัสผ่านที่ถูก hash (เรายังไม่ได้เพิ่ม user ที่ hash password)
        // สำหรับตอนนี้ เราจะเปรียบเทียบรหัสผ่านตรงๆ ก่อน
        // แต่ถ้าจะใช้งานจริง ต้องใช้ password_verify()
        if ($password === $user['password']) { // นี่คือการเปรียบเทียบแบบง่าย
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            header("location: /035-project3/chin1.html");
            exit;
        } else {
            echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }
    } else {
        echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
    $stmt->close();
}
$conn->close();
?>