<?php
session_start();

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
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $user['username'];
            header("location: /035-project3/chin1.php");
            exit;
        } else {
            // รหัสผ่านไม่ถูกต้อง
            header("location: index.html?error=invalid_credentials");
            exit();
        }
    } else {
        // ไม่พบผู้ใช้
        header("location: index.html?error=invalid_credentials");
        exit();
    }
    $stmt->close();
}
$conn->close();
?>