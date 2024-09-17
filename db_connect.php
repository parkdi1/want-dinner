<?php
$servername = "133.186.229.122";
$username = "your_username";
$password = "your_password";
$dbname = "dinner";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// UTF-8 인코딩 설정
$conn->set_charset("utf8mb4");
?>